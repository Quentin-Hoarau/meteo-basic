<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ville;
use AppBundle\Form\VilleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class MeteoController extends Controller
{
    /**
     * @Route("/", name="meteo")
     */
    public function indexAction()
    {
        // recupération de la Request
        $request = Request::createFromGlobals();

        // création du formulaire lié a l'entité Ville
        $form = $this->createForm(VilleType::class);

        // obligatoire pour le bon fonctionnement du form
        $form->handleRequest($request);

        // le formulaire est validé et bien envoyé
        if($form->isSubmitted() && $form->isValid()){
            // récupération des données envoyées par le formulaire (ici: la ville)
            $data = $form->getData();
            $villeNom = $data->getVilleNom();

            //recupere la ville en BDD
            $ville = $this->getDoctrine()->getRepository(Ville::class)->findOneBy(array("villeNom" => $villeNom));
            if(!$ville){
                throw $this->createNotFoundException("Cette ville n'existe pas...");
            }

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);
            $jsonContent = $serializer->serialize($ville, 'json');

            // récupération des données météo
            $meteo = $this->getMeteo('meteo', $ville->getVilleNom());
            $forecast = $this->getMeteo('forecast', $ville->getVilleNom());

            return $this->render('@App\Meteo\index.html.twig', array(
                "meteo" => $meteo,
                "forecast" => $forecast,
                "form" => $form->createView(),
                "ville" => $jsonContent,
            ));
        }

        return $this->render('@App\Meteo\index.html.twig', array(
            "form" => $form->createView()
        ));
    }

    /**
     * récupère les infos météo via l'api OpenWeatherMap
     *
     * @param $type -> type de données possibles de l'api OpenWeatherMap
     * @param $ville - nom de la ville
     *
     * @return bool|mixed|string
     */
    public function getMeteo($type, $ville)
    {
        // utilisation des paramètres enregistrés dans "parameters.yml"
        $api_key = $this->getParameter('api_key');
        $unit = $this->getParameter('unit');
        $lang = $this->getParameter('lang');

        // creation de l'url en fonction du type de données que l'on veut
        $url = "";
        if($type == "meteo"){
            $url = "https://api.openweathermap.org/data/2.5/weather?q=". $ville ."&appid=". $api_key ."&units=". $unit ."&lang=". $lang;
        }
        if($type == "forecast"){
            $cnt = 9;
            $url = "https://api.openweathermap.org/data/2.5/forecast?q=". $ville ."&appid=". $api_key ."&units=". $unit ."&lang=". $lang ."&cnt=". $cnt;
        }

        // vérifie si l'url existe (sinon 404)
        $response_header = @get_headers($url);
        if(!$response_header || $response_header[0] == "HTTP/1.1 404 Not Found"){
            throw $this->createNotFoundException("La ville demandée n'existe pas...");
        }

        // récupération des données de l'api (au format JSON ou non en fonction du type)
        $content = file_get_contents($url);
        $meteo = $content;
        if($type == "meteo"){
            $meteo = json_decode($content);
        }

        return $meteo;
    }
}

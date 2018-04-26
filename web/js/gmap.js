var map;
var ville = $('#forecast_datas').data('ville');

function initMap() {
    var coordonees = {lat: ville.villeLatitudeDeg, lng: ville.villeLongitudeDeg};
    map = new google.maps.Map(document.getElementById('map'), {
        center: coordonees,
        zoom: 11
    });
    var marker = new google.maps.Marker({
        position: coordonees,
        map: map
    });
}
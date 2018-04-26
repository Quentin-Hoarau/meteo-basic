// récupération des données météo
var forecast_city_name = $('#forecast_datas').data('forecast').city.name;
var forecast_datas_list = $('#forecast_datas').data('forecast').list;

console.log(forecast_datas_list);

// organisation des données a mettre dans le graphiques des températures
categories_array = [];
temperature_array = [];
$(forecast_datas_list).each(function(index){
    //little_array = [forecast_datas[index].main.temp, forecast_datas[index].dt_txt];
    temperature_array.push(forecast_datas_list[index].main.temp);
    categories_array.push(forecast_datas_list[index].dt_txt);
});

// graphique des températures
Highcharts.chart('forecast_container', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Température sur les prochains jours'
    },
    subtitle: {
        text: 'Source: OpenWeatherMap'
    },
    xAxis: {
        categories: categories_array
    },
    yAxis: {
        title: {
            text: 'Temperature (°C)'
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: false
        }
    },
    series: [{
        name: forecast_city_name,
        data: temperature_array
    }]
});
mapboxgl.accessToken = 'pk.eyJ1Ijoic2VyZ2lvcHIiLCJhIjoiY2szMGl5dnVoMHJxYTNia3RqanZ1aHY5ZyJ9.OTZtAakAldzCnHGAb0noJw';

window.addEventListener("DOMContentLoaded", async e => {
    let lat = parseFloat(document.getElementById('anuncio_latitud').value);
    let lng = parseFloat(document.getElementById('anuncio_longitud').value);

    let checkMkt;

    if(isNaN(lat) || isNaN(lng)) {
        lng = -0.517167;
        lat = 38.388020;
    }else{
        checkMkt = true;
    }

    let mapa = document.getElementById('map');

    let map = new mapboxgl.Map({
        container: mapa,
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [lng,lat],
        zoom: 13
    });
    if(checkMkt){
        let marker = new mapboxgl.Marker()
            .setLngLat([lng, lat])
            .addTo(map);
    }


    let geocoder = new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        mapboxgl: mapboxgl,
        marker: {color: 'LIGHTSKYBLUE'}
    });
    map.addControl(geocoder);
    geocoder.on('result', e => {
        document.getElementById('anuncio_longitud').value = e.result.center[0];
        document.getElementById('anuncio_latitud').value = e.result.center[1];
    })

});

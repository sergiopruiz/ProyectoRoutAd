let f_duration = 0;
document.getElementById('audio').addEventListener('canplaythrough', function(e){
    f_duration = Math.round(e.currentTarget.duration);
    document.getElementById('anuncio_duracion').value = f_duration;
    URL.revokeObjectURL(obUrl);
});

let obUrl;
document.getElementById('anuncio_video').addEventListener('change', function(e){
    let file = e.currentTarget.files[0];

    if(file.name.match(/\.(avi|mp3|mp4|mpeg|ogg)$/i)){
        obUrl = URL.createObjectURL(file);
        document.getElementById('audio').setAttribute('src', obUrl);
    }
});
let imageAnuncio;
let imagenTablet;
function loadImage(event) {
    let file = event.target.files[0];
    let reader = new FileReader();

    if (file) reader.readAsDataURL(file);

    reader.addEventListener('load', e => {
        document.getElementById('imagenPreview').src = reader.result;
    });
}

window.addEventListener("DOMContentLoaded", e => {

    imageAnuncio = document.getElementById("anuncio_imagen");
    imagenTablet = document.getElementById("tablet_imagenCorporativa");

    if(imageAnuncio){
        imageAnuncio.addEventListener('change', loadImage);
    }else{
        imagenTablet.addEventListener('change', loadImage);
    }

});
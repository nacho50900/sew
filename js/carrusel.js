"use strict";

class Carrusel {
    constructor(busqueda) {
        this.busqueda = busqueda;
        this.actual = 0;
        this.maximo = 4;
        this.fotos = [];
        
        this.getFotografias();
    }

    getFotografias() {
        // API de Unsplash
        $.getJSON("https://api.unsplash.com/search/photos", {
            client_id: "-KhXDWwnlFHMsdk2YI0iaQxT3alLPOLtVNzuK-O2W4g",
            query: this.busqueda,
            per_page: 5,
            orientation: "landscape"
        })
        .done((data) => {
            console.log("Datos recibidos de Unsplash:", data);
            this.procesarJSONFotografias(data);
        })
        .fail((jqXHR, textStatus, errorThrown) => {
            console.error("Error al cargar fotos:", textStatus, errorThrown);
            console.error("Detalles del error:", jqXHR);
        });
    }
    
    procesarJSONFotografias(data) {
        console.log("Procesando JSON...");
        if(data.results && data.results.length > 0) {
            // Formato de Unsplash -> formato Carrusel
            this.fotos = data.results.map(photo => ({
                url: photo.urls.regular,
                title: photo.alt_description || photo.description || "Imagen de MotoGP"
            }));
            console.log("Fotos encontradas:", this.fotos.length);
            this.mostrarFotografias();
        } else {
            console.error("No se encontraron fotos en la respuesta");
            console.log("Estructura de datos recibida:", data);
        }
    }

    mostrarFotografias() {
        console.log("Mostrando fotografías...");
        
        let article = $("<article>");
        let h2 = $("<h2>").text("Imágenes de " + this.busqueda);
        
        let foto = this.fotos[0];
        
        console.log("URL de la primera imagen:", foto.url);
        
        let img = $("<img>").attr("src", foto.url).attr("alt", foto.title);
        
        article.append(h2);
        article.append(img);
        
        $("header").after(article);
        
        console.log("Carrusel insertado en el DOM");
        
        setInterval(() => this.cambiarFotografia(), 3000);
    }

    cambiarFotografia() {
        this.actual++;
        if(this.actual > this.maximo) {
            this.actual = 0;
        }
        
        let foto = this.fotos[this.actual];
        
        console.log("Cambiando a imagen:", this.actual, foto.url);
        
        $("article img").attr("src", foto.url).attr("alt", foto.title);
    }
}

// Inicializar el carrusel cuando el documento esté listo
$(document).ready(() => {
    console.log("Documento listo, iniciando carrusel...");
    new Carrusel("MotoGP Sepang Circuit");
});
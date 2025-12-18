"use strict";

class Noticias {

    #busqueda;
    #url;

    constructor(busqueda) {
        this.#busqueda = busqueda;
        this.#url = "https://api.thenewsapi.com/v1/news/all";
        
        this.buscar();
    }

    buscar() {

        const apiKey = "AGRLeGwjezHCuRqO2zP9KpSKUDxJbDYxfSH3T3OJ"; 
        const url = `${this.#url}?api_token=${apiKey}&search=${encodeURIComponent(this.#busqueda)}&language=es&limit=5`;
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la petición: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                this.procesarInformacion(data);
            })
            .catch(error => {
                console.error('Error al obtener noticias:', error);
                this.#mostrarError();
            });
    }

    procesarInformacion(data) {
        if (!data.data || data.data.length === 0) {
            this.#mostrarSinNoticias();
            return;
        }

        let section = $("<section>");
        let h2 = $("<h2>").text("Noticias sobre " + this.#busqueda);
        section.append(h2);

        data.data.forEach(noticia => {
            let article = $("<article>");
            
            let h3 = $("<h3>").text(noticia.title);
            article.append(h3);
            
            if (noticia.description) {
                let p = $("<p>").text(noticia.description);
                article.append(p);
            }
            
            let fuente = $("<p>").html("<strong>Fuente:</strong> " + (noticia.source || "Desconocida"));
            article.append(fuente);
            
            if (noticia.url) {
                let enlace = $("<a>")
                    .attr("href", noticia.url)
                    .attr("target", "_blank")
                    .attr("rel", "noopener noreferrer")
                    .text("Leer noticia completa");
                let pEnlace = $("<p>").append(enlace);
                article.append(pEnlace);
            }
            
            section.append(article);
        });

        $("article").first().after(section);
    }

    #mostrarError() {
        let section = $("<section>");
        let h2 = $("<h2>").text("Noticias sobre " + this.#busqueda);
        let p = $("<p>").text("Error al cargar las noticias. Por favor, intenta más tarde.");
        section.append(h2);
        section.append(p);
        $("article").first().after(section);
    }

    #mostrarSinNoticias() {
        let section = $("<section>");
        let h2 = $("<h2>").text("Noticias sobre " + this.#busqueda);
        let p = $("<p>").text("No se encontraron noticias recientes.");
        section.append(h2);
        section.append(p);
        $("article").first().after(section);
    }
}

// Inicializar las noticias cuando el documento esté listo
$(document).ready(() => {
    console.log("Iniciando búsqueda de noticias...");
    new Noticias("MotoGP Sepang Circuit");
});
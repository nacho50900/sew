"use strict";

class Circuito {
    constructor() {
        this.comprobarApiFile();
    }

    comprobarApiFile() {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            console.log("API File soportada");
        } else {
            let mensaje = $("<p>").text("Tu navegador no soporta el API File. Por favor, actualiza tu navegador.");
            mensaje.css({
                "color": "red",
                "font-weight": "bold",
                "padding": "1em",
                "background-color": "#ffeeee",
                "border": "1px solid red",
                "margin": "1em"
            });
            $("body").append(mensaje);
        }
    }

    leerArchivoHTML(archivo) {
        if (!archivo) {
            return;
        }

        if (archivo.type !== "text/html") {
            alert("Por favor, selecciona un archivo HTML válido");
            return;
        }

        let lector = new FileReader();

        lector.onload = (evento) => {
            this.procesarArchivoHTML(evento.target.result);
        };

        lector.onerror = () => {
            alert("Error al leer el archivo HTML");
        };

        lector.readAsText(archivo);
    }

    procesarArchivoHTML(contenido) {
        let parser = new DOMParser();
        let htmlDoc = parser.parseFromString(contenido, "text/html");

        let section = $("<section>").attr("id", "seccion-html");

        let bodyContent = $(htmlDoc.body).children();
        
        if (bodyContent.length > 0) {
            let saltarSiguiente = false;
            
            bodyContent.each(function() {
                if ($(this).is("h2") && $(this).text().trim() === "Referencias") {
                    saltarSiguiente = true;
                    return;
                }
                
                if (saltarSiguiente) {
                    saltarSiguiente = false;
                    return;
                }
                
                let elemento = $(this);
                
                elemento.find("p, div").each(function() {
                    let texto = $(this).text().trim();
                    if (texto.match(/multimedia\/[^\s]+\.(jpg|png|gif|jpeg)/i)) {
                        let rutasImagenes = texto.split(/\s+/);
                        let contenedor = $("<p>");
                        
                        rutasImagenes.forEach(ruta => {
                            ruta = ruta.trim();
                            if (ruta && ruta.match(/multimedia\/[^\s]+\.(jpg|png|gif|jpeg)/i)) {
                                let img = $("<img>")
                                    .attr("src", ruta)
                                    .attr("alt", "Imagen del circuito")
                                    .css({"max-width": "300px", "height": "auto", "margin": "0.5em", "display": "inline-block"});
                                contenedor.append(img);
                            }
                        });
                        
                        $(this).replaceWith(contenedor);
                    }
                });
                
                elemento.find("ul, ol").each(function() {
                    let tieneVideos = false;
                    let rutasVideos = [];
                    
                    $(this).find("li").each(function() {
                        let texto = $(this).text().trim();
                        if (texto.match(/multimedia\/[^\s]+\.(mp4|webm|ogg)/i)) {
                            tieneVideos = true;
                            rutasVideos.push(texto);
                        }
                    });
                    
                    if (tieneVideos) {
                        let contenedorVideos = $("<div>");
                        rutasVideos.forEach(ruta => {
                            let video = $("<video>")
                                .attr("controls", "")
                                .css({"max-width": "100%", "height": "auto", "margin": "0.5em", "display": "block"});
                            
                            let source = $("<source>")
                                .attr("src", ruta)
                                .attr("type", "video/mp4");
                            
                            video.append(source);
                            video.append("Tu navegador no soporta el elemento de video.");
                            contenedorVideos.append(video);
                        });
                        $(this).replaceWith(contenedorVideos);
                    }
                });
                
                section.append(elemento.clone());
            });
            
        } else {
            section.append("<p>No se pudo cargar la información del circuito</p>");
        }

        $("#seccion-html").remove();
        
        $("section").first().remove();
        
        $("header").after(section);
        
        let seccionSVG = $("#seccion-svg");
        if (seccionSVG.length > 0) {
            seccionSVG.detach();
            section.after(seccionSVG);
        }
    }
}

class CargadorSVG {
    constructor() {
        this.svg = null;
    }

    leerArchivoSVG(archivo) {
        if (!archivo) {
            return;
        }

        if (archivo.type !== "image/svg+xml") {
            alert("Por favor, selecciona un archivo SVG válido");
            return;
        }

        let lector = new FileReader();

        lector.onload = (evento) => {
            this.svg = evento.target.result;
            this.insertarSVG();
        };

        lector.onerror = () => {
            alert("Error al leer el archivo SVG");
        };

        lector.readAsText(archivo);
    }

    insertarSVG() {
        if (!this.svg) {
            return;
        }

        let parser = new DOMParser();
        let svgDoc = parser.parseFromString(this.svg, "image/svg+xml");
        
        let errorNode = svgDoc.querySelector("parsererror");
        if (errorNode) {
            alert("Error al parsear el archivo SVG");
            return;
        }
        
        let svgElement = svgDoc.documentElement;

        $("#seccion-svg").remove();

        $("section").each(function() {
            if ($(this).find("input[type='file']#archivoSVG").length > 0) {
                $(this).remove();
            }
        });

        let section = $("<section>").attr("id", "seccion-svg");
        let heading = $("<h2>").text("Perfil de Altimetría del Circuito");
        section.append(heading);

        let contenedorSVG = $("<article>").css({
            "width": "100%",
            "overflow-x": "auto",
            "margin": "1em 0"
        });

        contenedorSVG.append(svgElement);
        section.append(contenedorSVG);

        let seccionHTML = $("#seccion-html");
        
        if (seccionHTML.length > 0) {
            seccionHTML.after(section);
        } else {
            let seccionMapa = $("#seccion-mapa");
            
            if (seccionMapa.length > 0) {
                seccionMapa.before(section);
            } else {
                let ultimaSeccionInput = $("section").filter(function() {
                    return $(this).find("input[type='file']").length > 0;
                }).last();
                
                if (ultimaSeccionInput.length > 0) {
                    ultimaSeccionInput.after(section);
                } else {
                    $("header").after(section);
                }
            }
        }
        
        console.log("Archivo SVG cargado y representado correctamente");
    }
}

class CargadorKML {
    constructor() {
        this.kml = null;
        this.coordenadasOrigen = null;
        this.coordenadasTramos = [];
        this.mapa = null;
    }

    leerArchivoKML(archivo) {
        if (!archivo) {
            return;
        }

        if (archivo.type !== "application/vnd.google-earth.kml+xml" && 
            archivo.type !== "text/xml" && 
            archivo.type !== "application/xml") {
            alert("Por favor, selecciona un archivo KML válido");
            return;
        }

        let lector = new FileReader();

        lector.onload = (evento) => {
            this.kml = evento.target.result;
            this.procesarKML();
        };

        lector.onerror = () => {
            alert("Error al leer el archivo KML");
        };

        lector.readAsText(archivo);
    }

    procesarKML() {
        if (!this.kml) {
            return;
        }

        let parser = new DOMParser();
        let kmlDoc = parser.parseFromString(this.kml, "text/xml");

        let errorNode = kmlDoc.querySelector("parsererror");
        if (errorNode) {
            alert("Error al parsear el archivo KML");
            console.error("Error de parsing:", errorNode);
            return;
        }

        console.log("KML parseado correctamente");

        let placemarks = kmlDoc.getElementsByTagName("Placemark");
        console.log("Placemarks encontrados:", placemarks.length);

        for (let i = 0; i < placemarks.length; i++) {
            let placemark = placemarks[i];
            
            let point = placemark.getElementsByTagName("Point")[0];
            if (point) {
                let coordinates = point.getElementsByTagName("coordinates")[0];
                if (coordinates) {
                    let coordText = coordinates.textContent.trim();
                    console.log("Coordenadas Point encontradas:", coordText);
                    let coords = coordText.split(",");
                    this.coordenadasOrigen = {
                        lng: parseFloat(coords[0]),
                        lat: parseFloat(coords[1])
                    };
                    console.log("Origen extraído:", this.coordenadasOrigen);
                }
            }
            
            let lineString = placemark.getElementsByTagName("LineString")[0];
            if (lineString) {
                let coordinates = lineString.getElementsByTagName("coordinates")[0];
                if (coordinates) {
                    let coordText = coordinates.textContent.trim();
                    console.log("Coordenadas LineString encontradas");
                    
                    let coordsArray = coordText.split(/[\s\n]+/);
                    
                    this.coordenadasTramos = coordsArray.map(coord => {
                        coord = coord.trim();
                        if (coord.length === 0) return null;
                        
                        let parts = coord.split(",");
                        if (parts.length >= 2) {
                            return {
                                lat: parseFloat(parts[1]),
                                lng: parseFloat(parts[0])
                            };
                        }
                        return null;
                    }).filter(coord => coord !== null && !isNaN(coord.lat) && !isNaN(coord.lng));
                    
                    console.log("Tramos extraídos:", this.coordenadasTramos.length, "puntos");
                    
                    if (!this.coordenadasOrigen && this.coordenadasTramos.length > 0) {
                        this.coordenadasOrigen = {
                            lat: this.coordenadasTramos[0].lat,
                            lng: this.coordenadasTramos[0].lng
                        };
                        console.log("Origen tomado del primer punto del LineString:", this.coordenadasOrigen);
                    }
                }
            }
        }

        if (!this.coordenadasOrigen) {
            alert("No se pudo extraer coordenadas del circuito. Verifica el formato del archivo KML.");
            console.error("El archivo KML no contiene coordenadas válidas");
            return;
        }

        if (this.coordenadasTramos.length === 0) {
            console.warn("No se encontraron tramos del circuito");
        }

        this.crearMapa();
    }

    crearMapa() {
        if (!this.coordenadasOrigen) {
            alert("No se pudo extraer el punto origen del circuito");
            return;
        }

        console.log("Creando mapa con origen:", this.coordenadasOrigen);

        let seccionMapa = $("#seccion-mapa");
        
        if (seccionMapa.length === 0) {

            let section = $("<section>").attr("id", "seccion-mapa");
            let heading = $("<h2>").text("Mapa del Circuito");
            let mapaDiv = $("<div>").attr("id", "mapa");
            
            section.append(heading);
            section.append(mapaDiv);
            
            $("section").each(function() {
                if ($(this).find("input[type='file']#archivoKML").length > 0) {
                    $(this).remove();
                }
            });
            
            $("body").append(section);
        }

        console.log("Div del mapa encontrado/creado, inicializando Google Maps...");

        this.inicializarMapa();
    }

    inicializarMapa() {
        let mapaDiv = document.getElementById("mapa");
        
        if (!mapaDiv) {
            console.error("No se encontró el div del mapa");
            alert("Error: No se pudo encontrar el contenedor del mapa");
            return;
        }

        console.log("Inicializando Google Maps...");
        console.log("Centro del mapa:", this.coordenadasOrigen);

        try {
            this.mapa = new google.maps.Map(mapaDiv, {
                center: this.coordenadasOrigen,
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.SATELLITE
            });

            console.log("Mapa creado correctamente");

            this.insertarCapaKML();
            
            console.log("Mapa cargado correctamente con " + this.coordenadasTramos.length + " puntos del circuito");
            
        } catch (error) {
            console.error("Error al crear el mapa:", error);
            alert("Error al inicializar Google Maps: " + error.message);
        }
    }

    insertarCapaKML() {
        if (!this.mapa) {
            console.error("El mapa no está inicializado");
            return;
        }

        console.log("Insertando capa KML en el mapa...");

        if (this.coordenadasOrigen) {

            const marker = new google.maps.Marker({
                position: this.coordenadasOrigen,
                map: this.mapa,
                title: "Origen del Circuito",
                icon: {
                    url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                }
            });
            console.log("Marcador de origen colocado");
        }

        if (this.coordenadasTramos.length > 0) {
            const polyline = new google.maps.Polyline({
                path: this.coordenadasTramos,
                geodesic: true,
                strokeColor: "#FF0000",
                strokeOpacity: 1.0,
                strokeWeight: 3,
                map: this.mapa
            });
            console.log("Polilínea del circuito dibujada con " + this.coordenadasTramos.length + " puntos");
        } else {
            console.warn("No hay coordenadas de tramos para dibujar");
        }

        console.log("Capa KML insertada en el mapa correctamente");
    }
}

// Inicialización cuando el DOM esté listo
$(document).ready(function() {
    
    let circuito = new Circuito();
    let cargadorSVG = new CargadorSVG();
    let cargadorKML = new CargadorKML();
    
    $("#archivoHTML").on("change", function() {
        if (this.files.length > 0) {
            circuito.leerArchivoHTML(this.files[0]);
        }
    });
    
    $("#archivoSVG").on("change", function() {
        if (this.files.length > 0) {
            cargadorSVG.leerArchivoSVG(this.files[0]);
        }
    });
    
    $("#archivoKML").on("change", function() {
        if (this.files.length > 0) {
            cargadorKML.leerArchivoKML(this.files[0]);
        }
    });
});
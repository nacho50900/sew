"use strict";

class Ciudad {

    #nombre;
    #pais;
    #gentilicio;
    #poblacion;
    #coordenadas;

    constructor(nombre, pais, gentilicio) {
        this.#nombre = nombre;
        this.#pais = pais;
        this.#gentilicio = gentilicio;
        this.#poblacion = null;
        this.#coordenadas = null;
    }

    rellenarAtributos(poblacion, lat, lon) {
        this.#poblacion = poblacion;
        this.#coordenadas = {lat: lat, lon: lon};
    }

    getNombre() {
        return this.#nombre;
    }

    getPais() {
        return this.#pais;
    }

    getInfoSecundaria() {
        let html = "<ul>";
        html += "<li>Gentilicio: " + this.#gentilicio + "</li>";
        html += "<li>Población: " + this.#poblacion.toLocaleString() + "</li>";
        html += "</ul>";
        return html;
    }

    escribirCoordenadas() {
        let p = document.createElement("p");
        p.innerHTML = "Coordenadas: " + this.#coordenadas.lat + ", " + this.#coordenadas.lon;
        document.querySelector("main").appendChild(p);
    }

    // METEOROLOGÍA DÍA DE CARRERA

    getMeteorologiaCarrera(fecha) {
        $.getJSON("https://archive-api.open-meteo.com/v1/archive", {
            latitude: this.#coordenadas.lat,
            longitude: this.#coordenadas.lon,
            start_date: fecha,
            end_date: fecha,
            hourly: "temperature_2m,apparent_temperature,rain,relative_humidity_2m,wind_speed_10m,wind_direction_10m",
            daily: "sunrise,sunset",
            timezone: "auto"
        }).done(data => {
            this.procesarJSONCarrera(data);
        }).fail(() => {
            console.error("Error al obtener datos meteorológicos de la carrera");
        });
    }

    procesarJSONCarrera(data) {

        let section = $("<section>");
        section.append("<h3>Meteorología día de carrera</h3>");

        let p = $("<p>");
        p.html("Amanecer: " + data.daily.sunrise[0] + "<br>Atardecer: " + data.daily.sunset[0]);
        section.append(p);
        
        let tabla = "<table><thead><tr>";
        tabla += "<th scope='col'>Hora</th>";
        tabla += "<th scope='col'>Temp</th>";
        tabla += "<th scope='col'>Sensación</th>";
        tabla += "<th scope='col'>Lluvia</th>";
        tabla += "<th scope='col'>Humedad</th>";
        tabla += "<th scope='col'>Viento</th>";
        tabla += "<th scope='col'>Dir</th>";
        tabla += "</tr></thead><tbody>";
        
        for(let i = 0; i < data.hourly.time.length; i += 3) {
            let hora = data.hourly.time[i].split("T")[1];
            tabla += "<tr>";
            tabla += "<td data-label='Hora'>" + hora + "</td>";
            tabla += "<td data-label='Temp'>" + data.hourly.temperature_2m[i] + " °C</td>";
            tabla += "<td data-label='Sensación'>" + data.hourly.apparent_temperature[i] + " °C</td>";
            tabla += "<td data-label='Lluvia'>" + data.hourly.rain[i] + " mm</td>";
            tabla += "<td data-label='Humedad'>" + data.hourly.relative_humidity_2m[i] + " %</td>";
            tabla += "<td data-label='Viento'>" + data.hourly.wind_speed_10m[i] + " km/h</td>";
            tabla += "<td data-label='Dir'>" + data.hourly.wind_direction_10m[i] + "°</td>";
            tabla += "</tr>";
        }
        tabla += "</tbody></table>";
        
        section.append(tabla);
        $("main").append(section);
    }

    // METEOROLOGÍA DÍAS DE ENTRENAMIENTOS

    getMeteorologiaEntrenos(inicio, fin) {
        $.getJSON("https://archive-api.open-meteo.com/v1/archive", {
            latitude: this.#coordenadas.lat,
            longitude: this.#coordenadas.lon,
            start_date: inicio,
            end_date: fin,
            hourly: "temperature_2m,rain,wind_speed_10m,relative_humidity_2m",
            timezone: "auto"
        }).done(data => {
            this.procesarJSONEntrenos(data);
        }).fail(() => {
            console.error("Error al obtener datos meteorológicos de entrenamientos");
        });
    }

    procesarJSONEntrenos(data) {
        let dias = {};
        
        data.hourly.time.forEach((t, i) => {
            let fecha = t.split("T")[0];
            if(!dias[fecha]) {
                dias[fecha] = {temps: [], lluvias: [], vientos: [], humedades: []};
            }
            dias[fecha].temps.push(data.hourly.temperature_2m[i]);
            dias[fecha].lluvias.push(data.hourly.rain[i]);
            dias[fecha].vientos.push(data.hourly.wind_speed_10m[i]);
            dias[fecha].humedades.push(data.hourly.relative_humidity_2m[i]);
        });
        
        let section = $("<section>");
        section.append("<h3>Meteorología entrenamientos (medias)</h3>");
        
        let tabla = "<table><thead><tr>";
        tabla += "<th scope='col'>Día</th>";
        tabla += "<th scope='col'>Temp</th>";
        tabla += "<th scope='col'>Lluvia</th>";
        tabla += "<th scope='col'>Viento</th>";
        tabla += "<th scope='col'>Humedad</th>";
        tabla += "</tr></thead><tbody>";
        
        for(let fecha in dias) {
            let d = dias[fecha];
            let tempMedia = (d.temps.reduce((a,b) => a+b) / d.temps.length).toFixed(2);
            let lluviaMedia = (d.lluvias.reduce((a,b) => a+b) / d.lluvias.length).toFixed(2);
            let vientoMedia = (d.vientos.reduce((a,b) => a+b) / d.vientos.length).toFixed(2);
            let humedadMedia = (d.humedades.reduce((a,b) => a+b) / d.humedades.length).toFixed(2);
            
            tabla += "<tr>";
            tabla += "<td data-label='Día'>" + fecha + "</td>";
            tabla += "<td data-label='Temp'>" + tempMedia + " °C</td>";
            tabla += "<td data-label='Lluvia'>" + lluviaMedia + " mm</td>";
            tabla += "<td data-label='Viento'>" + vientoMedia + " km/h</td>";
            tabla += "<td data-label='Humedad'>" + humedadMedia + " %</td>";
            tabla += "</tr>";
        }
        tabla += "</tbody></table>";
        
        section.append(tabla);
        $("main").append(section);
    }
}
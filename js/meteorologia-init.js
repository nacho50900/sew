"use strict";

$(document).ready(function() {
    let ciudad = new Ciudad("Sepang", "Malasia", "Sepang");
    ciudad.rellenarAtributos(231752, 2.7606, 101.7375);
    
    let main = $("main");
    main.append("<p>Ciudad: " + ciudad.getNombre() + "</p>");
    main.append("<p>País: " + ciudad.getPais() + "</p>");
    main.append(ciudad.getInfoSecundaria());
    ciudad.escribirCoordenadas();
    
    ciudad.getMeteorologiaCarrera("2025-10-26");
    ciudad.getMeteorologiaEntrenos("2025-10-24", "2025-10-26");
});
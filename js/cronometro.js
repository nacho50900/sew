"use strict";

class Cronometro {
    constructor() {
        this.tiempo = 0;
        this.inicio = null;
        this.corriendo = null;
        this.usandoTemporal = false;
    }

    _ahoraMs() {
        if (this.usandoTemporal) {
            return Number(Temporal.Now.instant().epochNanoseconds / 1_000_000n);
        }
        return Date.now();
    }

    _inicioMs() {
        if (this.usandoTemporal) {
            return Number(this.inicio.epochNanoseconds / 1_000_000n);
        }
        return this.inicio.getTime();
    }

    arrancar() {
        if (this.corriendo !== null) return;
        try {
            if (typeof Temporal === "undefined") throw new Error();
            this.usandoTemporal = true;
            this.inicio = Temporal.Now.instant();
        } catch {
            this.usandoTemporal = false;
            this.inicio = new Date();
        }
        this.actualizar();
        this.corriendo = setInterval(this.actualizar.bind(this), 100);
    }

    actualizar() {
        if (!this.inicio) return;
        this.tiempo = Math.max(0, this._ahoraMs() - this._inicioMs());
        this.mostrar();
    }

    mostrar() {
        const ms = Math.trunc(this.tiempo);
        const min = Math.trunc(ms / 60000);
        const seg = Math.trunc((ms % 60000) / 1000);
        const dec = Math.trunc((ms % 1000) / 100);
        const mm = String(min).padStart(2, "0");
        const ss = String(seg).padStart(2, "0");
        
        const p = document.querySelector("main p");
        if (p) {
            p.textContent = `${mm}:${ss}.${dec}`;
        }
    }

    parar() {
        if (this.corriendo !== null) {
            clearInterval(this.corriendo);
            this.corriendo = null;
        }
    }

    reiniciar() {
        this.parar();
        this.tiempo = 0;
        this.inicio = null;
        this.mostrar();
    }

    inicializarEventos() {
        const botones = document.querySelectorAll("main button");
        
        if (botones.length >= 3) {

            botones[0].addEventListener("click", () => this.arrancar());
            
            botones[1].addEventListener("click", () => this.parar());
            
            botones[2].addEventListener("click", () => this.reiniciar());
            
            console.log("Eventos del cronómetro inicializados correctamente");
        } else {
            console.error("No se encontraron los botones del cronómetro");
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const cronometro = new Cronometro();
    cronometro.inicializarEventos();
    
    window.cronometro = cronometro;
});
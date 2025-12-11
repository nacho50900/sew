"use strict";

class Memoria {
    constructor() {
        this.tablero_bloqueado = true;
        this.primera_carta = null;
        this.segunda_carta = null;

        this.barajarCartas();
        this.añadirEventos();
        
        this.crono = new Cronometro();
        this.crono.arrancar();
        
        this.tablero_bloqueado = false;
    }

    voltearCarta(carta) {
        if(carta.dataset.estado == "volteada" || 
           carta.dataset.estado == "revelada" || 
           this.tablero_bloqueado) {
            return;
        }

        carta.dataset.estado = "volteada";

        if(!this.primera_carta) {
            this.primera_carta = carta;
        } else {
            this.segunda_carta = carta;
            this.checkPareja();
        }
    }

    checkPareja() {
        let img1 = this.primera_carta.querySelector("img").src;
        let img2 = this.segunda_carta.querySelector("img").src;

        if(img1 == img2) {
            this.marcarPareja();
        } else {
            this.voltearBoca();
        }
    }

    voltearBoca() {
        this.tablero_bloqueado = true;

        setTimeout(() => {
            this.primera_carta.removeAttribute("data-estado");
            this.segunda_carta.removeAttribute("data-estado");
            this.resetAtributos();
        }, 1500);
    }

    marcarPareja() {
        this.primera_carta.dataset.estado = "revelada";
        this.segunda_carta.dataset.estado = "revelada";
        
        this.checkFin();
        this.resetAtributos();
    }

    resetAtributos() {
        this.tablero_bloqueado = false;
        this.primera_carta = null;
        this.segunda_carta = null;
    }

    checkFin() {
        let cartas = document.querySelectorAll("h2 + main article");
        let todasReveladas = true;
        
        cartas.forEach(c => {
            if(c.dataset.estado != "revelada") {
                todasReveladas = false;
            }
        });

        if(todasReveladas) {
            this.tablero_bloqueado = true;
            this.crono.parar();
            setTimeout(() => alert("¡Has ganado!"), 300);
        }
    }

    barajarCartas() {
        let main = document.querySelector("h2 + main");
        let cartas = Array.from(main.querySelectorAll("article"));

        for(let i = cartas.length - 1; i > 0; i--) {
            let j = Math.floor(Math.random() * (i + 1));
            let temp = cartas[i];
            cartas[i] = cartas[j];
            cartas[j] = temp;
        }

        cartas.forEach(c => main.appendChild(c));
    }

    añadirEventos() {
        let cartas = document.querySelectorAll("h2 + main article");
        cartas.forEach(carta => {
            carta.addEventListener("click", () => this.voltearCarta(carta));
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    new Memoria();
});
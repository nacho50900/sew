"use strict";

class MenuMovil {
    constructor() {
        this.menuDesplegado = false;
        this.inicializar();
    }

    inicializar() {

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.configurarMenu());
        } else {
            this.configurarMenu();
        }
    }

    configurarMenu() {

        const header = document.querySelector('header');
        const nav = header ? header.querySelector('nav') : null;
        
        if (!nav) {
            console.error('No se encontró el elemento nav en el header');
            return;
        }

        const botonMenu = document.createElement('button');
        botonMenu.setAttribute('type', 'button');
        botonMenu.setAttribute('aria-label', 'Abrir menú de navegación');
        botonMenu.setAttribute('aria-expanded', 'false');
        botonMenu.setAttribute('aria-controls', 'menu-navegacion');
        botonMenu.textContent = '☰ Menú';
        
        nav.setAttribute('id', 'menu-navegacion');
        nav.setAttribute('aria-hidden', 'true');

        header.insertBefore(botonMenu, nav);

        botonMenu.addEventListener('click', () => this.alternarMenu(botonMenu, nav));

        const enlaces = nav.querySelectorAll('a');
        enlaces.forEach(enlace => {
            enlace.addEventListener('click', () => {
                if (this.menuDesplegado) {
                    this.alternarMenu(botonMenu, nav);
                }
            });
        });

        window.addEventListener('resize', () => this.manejarResize(botonMenu, nav));
    }

    alternarMenu(boton, nav) {
        this.menuDesplegado = !this.menuDesplegado;
        
        if (this.menuDesplegado) {
            nav.classList.add('menu-visible');
            nav.setAttribute('aria-hidden', 'false');
            boton.setAttribute('aria-expanded', 'true');
            boton.setAttribute('aria-label', 'Cerrar menú de navegación');
            boton.textContent = '✕ Cerrar';
        } else {
            nav.classList.remove('menu-visible');
            nav.setAttribute('aria-hidden', 'true');
            boton.setAttribute('aria-expanded', 'false');
            boton.setAttribute('aria-label', 'Abrir menú de navegación');
            boton.textContent = '☰ Menú';
        }
    }

    manejarResize(boton, nav) {

        if (window.innerWidth > 485) {
            nav.classList.remove('menu-visible');
            nav.setAttribute('aria-hidden', 'false');
            this.menuDesplegado = false;
            boton.setAttribute('aria-expanded', 'false');
        } else {
            nav.setAttribute('aria-hidden', this.menuDesplegado ? 'false' : 'true');
        }
    }
}

if (typeof window !== 'undefined') {
    new MenuMovil();
}
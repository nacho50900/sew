"use strict";

class MenuMovil {
    constructor() {
        this.menuDesplegado = false;
        this.inicializar();
    }

    inicializar() {
        // Esperar a que el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.configurarMenu());
        } else {
            this.configurarMenu();
        }
    }

    configurarMenu() {
        // Obtener el nav del header
        const header = document.querySelector('header');
        const nav = header ? header.querySelector('nav') : null;
        
        if (!nav) {
            console.error('No se encontró el elemento nav en el header');
            return;
        }

        // Crear el botón de menú (hamburguesa)
        const botonMenu = document.createElement('button');
        botonMenu.setAttribute('type', 'button');
        botonMenu.setAttribute('aria-label', 'Abrir menú de navegación');
        botonMenu.setAttribute('aria-expanded', 'false');
        botonMenu.setAttribute('aria-controls', 'menu-navegacion');
        botonMenu.textContent = '☰ Menú';
        
        // Añadir ID al nav para accesibilidad
        nav.setAttribute('id', 'menu-navegacion');
        nav.setAttribute('aria-hidden', 'true');

        // Insertar el botón antes del nav
        header.insertBefore(botonMenu, nav);

        // Configurar el evento click del botón
        botonMenu.addEventListener('click', () => this.alternarMenu(botonMenu, nav));

        // Cerrar menú al hacer clic en un enlace
        const enlaces = nav.querySelectorAll('a');
        enlaces.forEach(enlace => {
            enlace.addEventListener('click', () => {
                if (this.menuDesplegado) {
                    this.alternarMenu(botonMenu, nav);
                }
            });
        });

        // Cerrar menú al redimensionar la ventana a tamaño mayor
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
        // Si la ventana es mayor a 485px, asegurar que el menú esté en estado normal
        if (window.innerWidth > 485) {
            nav.classList.remove('menu-visible');
            nav.setAttribute('aria-hidden', 'false');
            this.menuDesplegado = false;
            boton.setAttribute('aria-expanded', 'false');
        } else {
            // En móvil, mantener el estado actual
            nav.setAttribute('aria-hidden', this.menuDesplegado ? 'false' : 'true');
        }
    }
}

// Crear instancia del menú móvil cuando el documento esté listo
if (typeof window !== 'undefined') {
    new MenuMovil();
}
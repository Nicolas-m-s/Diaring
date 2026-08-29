// script.js
document.addEventListener('DOMContentLoaded', () => {
    const botonesEliminar = document.querySelectorAll('.btn-eliminar');

    botonesEliminar.forEach(boton => {
        boton.addEventListener('click', () => {
            const id = boton.dataset.id;
            const formulario = document.querySelector(`.form-overlay[data-id="${id}"]`);
            formulario.classList.add('activo');
        });
    });
});
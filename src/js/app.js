const mobileMenuBtn = document.querySelector('#mobile-menu'); //selecciona la imagen del icono
const sidebar = document.querySelector('.sidebar'); //seleccoina el sidebar
const cerrarMenuBtn = document.querySelector('#cerrar-menu');
if(mobileMenuBtn){ //como no siempre existe. Si existe
    mobileMenuBtn.addEventListener('click', function(){
        sidebar.classList.toggle('mostrar');
    })
}

if(cerrarMenuBtn){ //verifica que exista el boton de cerrar
    cerrarMenuBtn.addEventListener('click', function(){ //agrega el evento
        sidebar.classList.add('ocultar'); //agrega ocultar y permite la transición

        setTimeout(() => {
            sidebar.classList.remove('mostrar'); //quita mostrar,     
            sidebar.classList.remove('ocultar');
        }, 1000);
    }); 
}
//Elimina la clase de mostrar en un tamañoñ de tablet y mayores
const anchoPantalla = document.body.clientWidth;
window.addEventListener('resize', function(){
    const anchoPantalla = document.body.clientWidth;
    if(anchoPantalla >= 768){
        sidebar.classList.remove('mostrar');
    }
});
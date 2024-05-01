(function(){
    obtenerTareas();
    let tareas = []; //array de tareas
    let filtradas = []; //array para las tareas filtradas
    //Boton para mostrar el model de agregar tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea'); //selecciona el boton de agregar tarea
    nuevaTareaBtn.addEventListener('click', function(){
        mostrarFormulario();//se puede pasar sin argumento
    }); //evento de click

    //filtros  de búsquedas
    const filtros = document.querySelectorAll('#filtros input[type=radio]'); //se seleccionan los 3 inputs
    filtros.forEach(radio => { //por cada filtro itera
        radio.addEventListener('input', filtrarTareas); //vigila cada input y le asigna una funcion. Lleva implícito un evento
    });
    //funcion ejecutada cada que se selecciona un input radious
    function filtrarTareas(e){
        const filtro = e.target.value;
        if(filtro !== ''){ //se quieren filtrar
            filtradas = tareas.filter(tarea => tarea.estado === filtro); //filtra las tareas que sean igual a lo seleccionado en el radious
        }else{ //se van a mostrar todas las tareas y no hay tareas filtrada
            filtradas = [];  
        }

        mostrarTareas();
    }

    //comienzan las funciones
    async function obtenerTareas(){

        try {
            const id = obtenerProyecto(); //obtiene la url del querystring
            const url = `${location.origin}/api/tareas?id=${id}`;
            const respuesta = await fetch(url); //espera conexion a la api
            // console.log(respuesta); //status:200 conexion ok
            const resultado = await respuesta.json()
            // const {tareas} = resultado; <-antes del virtual dom
            tareas = resultado.tareas; //la variable global
            // console.log(tareas);
            mostrarTareas();
            
        } catch (error) {
            console.log(error);
        }
    }
    //funcion para desplegar las tareas o mostrar que no hay tareas
    function mostrarTareas(){
        limpiarTareas();
        totalPendientes();
        totalCompletas();
    // arreglo nuevo, condicional ? resultado verdadero, resultado falso
        const arrayTareas = (filtradas.length>0) ? filtradas : tareas;
        
        // console.log(tareas);
        if(arrayTareas.length === 0){ //si el proyecto no tiene tareas
            const contenedorTareas = document.querySelector('#listado-tareas');
            const textoNoTareas = document.createElement('LI');
            textoNoTareas.textContent = 'No hay tareas';
            textoNoTareas.classList.add('no-tareas');

            contenedorTareas.appendChild(textoNoTareas); 
        }else{
            const contenedorTareas = document.querySelector('#listado-tareas');
            const textoTareas = document.createElement('P');
            textoTareas.textContent = 'Da doble clic en pendiente o eliminar tarea';
            textoTareas.classList.add('texto-tareas');
            contenedorTareas.appendChild(textoTareas);
        }
        //***objeto de definicion de estados
        const estados = {
            0: 'Pendiente',
            1: 'Completa'
        }
        //iterando en las tareas
        arrayTareas.forEach(tarea =>{ //el argumento es tarea, es cada elemento en el que se itera
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id; //crea un atributo data-tarea.id
            contenedorTarea.classList.add('tarea');
            // console.log(contenedorTarea);
            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre; //agrtrueega el nombre de la tarea al p //true significa que se va a editar
            nombreTarea.ondblclick = function(){
                mostrarFormulario(editar = true, {...tarea}); //true significa que se va a editar
            }
            
            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            //botones cambiar tarea
            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`); //Crea clase con el estado y en minusculas
            btnEstadoTarea.textContent = estados[tarea.estado]; //toma el valor de la definición de estados-> estado.0 o estado.1
            btnEstadoTarea.dataset.estadoTarea = tarea.estado; //atributo personalizado
            btnEstadoTarea.ondblclick = function(){
                cambiarEstadoTarea({...tarea}); //se pasa copia dde tarea
            }
            //  boton eliminar tarea
            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = 'Eliminar';
            btnEliminarTarea.ondblclick = function(){
                confirmarEliminarTarea({...tarea});
            }
            
            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);
            
            contenedorTarea.appendChild(nombreTarea); //nombre de la tarea
            contenedorTarea.appendChild(opcionesDiv); //
            
            const listadoTareas = document.querySelector('#listado-tareas');
            listadoTareas.appendChild(contenedorTarea);
        })
    }

    function totalPendientes(){
        //calcula las tareas pendientes
        const totalPendientes = tareas.filter(tarea => tarea.estado === "0");
        // console.log(totalPendientes); //imprime el array con los pendientes
        const pendientesRadio = document.querySelector('#pendientes');
        if(totalPendientes.length ===0){
            pendientesRadio.disabled = true;
        }else{
            pendientesRadio.disabled = false;
        }
    }
    function totalCompletas(){
        //calculas las tareas completas
        const totalCompletas = tareas.filter(tarea => tarea.estado === "1");
        const completasRadio = document.querySelector('#completadas');
        if(totalCompletas.length ===0){
            completasRadio.disabled = true;
        }else{
            completasRadio.disabled = false;
        }
    }

    //funcion para mostrar el modal
    function mostrarFormulario( editar = false, tarea={} ){
        // console.log(tarea);
        const modal = document.createElement('DIV'); //crea un div
        modal.classList.add('modal'); //le agrega la clase modal al div
        modal.innerHTML = ` 
            <form class="formulario nueva-tarea">
                <legend>${editar ? 'Editar Tarea' : 'Añade una tarea'}</legend>
                <div class="campo">
                    <label>Tarea</label>
                    <input type="text" name="tarea" placeholder="${tarea.nombre ? 'Editar la tarea' : 'Añadir Tarea al Proyecto Actual'}" id="tarea" value="${tarea.nombre ?? ''}"/>
                </div>
                <div class="opciones">
                    <input type="submit" class="submit-nueva-tarea" value="${tarea.nombre ? 'Editar Tarea' : 'Añadir Tarea'}"/>
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `; // Le agrega un formulario al div modal

        setTimeout(() => {
            const formulario = document.querySelector('.formulario'); //selecciona clase del formulario pasados los 3s
            formulario.classList.add('animar'); //agrega la clase animar
        }, 0); //esto permmite que el formulario baje

        //espera por un clic en el modal
        modal.addEventListener('click', function(e){
            e.preventDefault(); //previene que se cierre la ventana al dar clic en añadir tarea
            //si se da clic a cancelar
            if(e.target.classList.contains('cerrar-modal') ) { //si el objeto al que se le da clic contiene la clase "cerrar-modal"
                const formulario = document.querySelector('.formulario'); //selecciona clase del formulario
                formulario.classList.add('cerrar'); //agrega la clase cerrar
                setTimeout(() => {
                    modal.remove();
                }, 800);
            }
            //si se da clic a añadir tarea
            if(e.target.classList.contains('submit-nueva-tarea')){
                //validacion del submit
                //selecciona id #tarea del input text, selecciona su valor y corta los espacios en blanco al inicio y al final
                const nombreTarea = document.querySelector('#tarea').value.trim(); //nombreTarea se crea para no sustituir el objeto tarea
                if(nombreTarea===''){ //si el valor del input está vacío
                    //Mostrar una alerta de error
                    mostrarAlerta( 'El nombre de la tarea es Obligatorio', 'error', 
                    document.querySelector('.formulario legend')); //clase formulario elemento legend
                    return;
                } 
                //discierne si se edita o crea
                if(editar){
                    tarea.nombre = nombreTarea;
                    actualizarTarea(tarea);
                }else{
                    agregarTarea(nombreTarea);
                }
            }
        });

        document.querySelector('.dashboard').appendChild(modal);

    }
    // function submitFormularioNuevaTarea(){
        //quitamos esta funcion para poner el codigo donde se llama
    //     agregarTarea(tarea); 
    // }
    //muestra un mensaje en la interfaz
    function mostrarAlerta(mensaje, tipo, referencia){
        //Previene que se creen múltiples alertas
        const alertaPrevia = document.querySelector('.alerta');
        if(alertaPrevia){
            alertaPrevia.remove();
        }
        
        const alerta = document.createElement('DIV'); //crea un div
        alerta.classList.add('alerta', tipo); //le agrega dos clases, alerta y tipo
        alerta.textContent=mensaje;

        //inserta la alerta antes del legend
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling); 

        //eliminar la alerta después de 5seg
        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }
    //consultar el servidor para añadir una nueva tarea al proyecto actual
    async function agregarTarea(tarea){
        //Construir la peticion
        const datos = new FormData(); //crea objeto formdata
        datos.append('nombre', tarea); //agrega un elemento clave-dato
        datos.append('proyectoId', obtenerProyecto()); //agrega la url del query string
        

        // return;
        //try -catch permite la ejecución del programa aunque no se concte al servidor
        try {
            const url = `${location.origin}/api/tarea`; //direccion a donde se envía la información
            const respuesta = await fetch(url, {  //respuesta a la conexión qque se está probando. El await espera la conexion a la api
                                                //no se ejecuta lo que sigue hasta que se cumpla la promesa del await
                method: 'POST', //post
                body: datos
            });
            const resultado = await respuesta.json();  //respuesta del servidor al json_encode
            // console.log(resultado); //imprime la respuesta del json_encode
            mostrarAlerta( resultado.mensaje, resultado.tipo, 
            document.querySelector('.formulario legend')); //clase formulario elemento legend
            if(resultado.tipo === 'exito'){ //se hizo la consulta con exito
                const modal = document.querySelector('.modal'); //seleccoina la clase del modal
                setTimeout(() => { //activa temporizador de 3seg
                    modal.remove(); //cierra el modal
                }, 3000);

                //agregar el objeto de tarea al global de tareas
                const tareaObj = {
                    id: String(resultado.id), //guarda el id del resultado de la tarea guardada
                    nombre: tarea,
                    estado: "0", //siempre es 0 porque es tarea nueva
                    proyectoId: resultado.proyectoId //id del proyecto, no la url
                }
                tareas = [...tareas, tareaObj]; //toma una copia del arreglo tareas
                mostrarTareas();
            }
        } catch (error) {
            console.log(error);
        }
    }
    //funcion para cambiar el estado de la copia de la tarea
    function cambiarEstadoTarea(tarea){ // se pasa la copia de tareas
        //si tarea.estado === 1 etonces nuevoEstado = 1, si tareaEstado === 0, entonces nuevoEstado = 1
        const nuevoEstado = tarea.estado === "1" ? "0" : "1";   
        tarea.estado = nuevoEstado; //asigna a copia tarea.estado el nuevo estado
        actualizarTarea(tarea);
    }
    //se actualiza el estado de la tarea
    async function actualizarTarea(tarea){
        // console.log(tarea); // prueba que tarea.nombre se actualiza
        // return;
        const {estado, id, nombre, proyectoId} = tarea;
        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto()); //realmente es la url
        try {
            const url = `${location.origin}/api/tarea/actualizar`;
            const respuesta = await fetch(url, { //por default acepta un get, se cambia a post
                 method: 'POST',
                 body: datos
            });
            const resultado = await respuesta.json();

            if(resultado.respuesta.tipo === 'exito'){ //la variable, el objeto y su atributo
                // mostrarAlerta(
                //     resultado.respuesta.mensaje,
                //     resultado.respuesta.tipo,
                //     document.querySelector('.contenedor-nueva-tarea')
                // );
                Swal.fire(
                    resultado.respuesta.mensaje,
                    resultado.respuesta.mensaje,
                    'success'
                );
                const modal = document.querySelector('.modal');
                if(modal){
                    modal.remove();
                }
                //virtual dom
                tareas = tareas.map(tareaMemoria =>{ //itera en tareas y crea la variable temporal tareaMemoria
                    if(tareaMemoria.id === id){ //evalua cual id de la tarea es igual a las tareas de la itracion
                        tareaMemoria.estado = estado; //asigna el nuevo estado de la tarea que cumple la igualdad
                        tareaMemoria.nombre = nombre;
                    }
                    return tareaMemoria; //retorna el arreglo en memoria para qe se asigne a tareas global
                });
                // console.log(tareas); //imprime el arreglo global ya modificado
                mostrarTareas(); //actualiza el html mostrando las tareas
            }
            
        } catch (error) {
            console.log(error);
        }
    }
    //codigo de la ventana de confirmación de eliminar
    function confirmarEliminarTarea(tarea){
        Swal.fire({
            title: '¿Eliminar tarea?',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No'
        }).then((result) => { 
            if (result.isConfirmed) {   //si se confirma eliminar
                eliminarTarea(tarea);
            }
          })
    }
    //
    async function eliminarTarea(tarea){

        const {estado, id, nombre, proyectoId} = tarea;

        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerProyecto()); //realmente es la url
        
        try {
            const url = `${location.origin}/api/tarea/eliminar`;
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            // console.log(respuesta); //respuesta a la conexión
            const resultado = await respuesta.json(); //espera la respuesta del jsonencode
            // console.log(resultado); 

            if(resultado.resultado){ //si se recibe la respuesta
                // mostrarAlerta( //muestra la alerta
                //     resultado.mensaje,
                //     resultado.tipo,
                //     document.querySelector('.contenedor-nueva-tarea')
                // );
                Swal.fire('Eliminado!', resultado.mensaje, 'success');

                //actializar el virtual DOM
                tareas = tareas.filter(tareaMemoria => tareaMemoria.id !== tarea.id); //extrae todas las tareas que son diferentes a la que se eliminó
                //filter no requiere el return porque no tiene un if dentro
                mostrarTareas();
            }
        
            
        } catch (error) {
            console.log(error);
        }
    }

    //función para extraer la url  de la url 
    function obtenerProyecto(){
        const proyectoParams = new URLSearchParams(window.location.search); //crea un objeto con la url
        const proyecto = Object.fromEntries(proyectoParams.entries()); //revisa los elementos de la url o query string
        return proyecto.id //retorna el atributo id
    }
    function limpiarTareas(){
        const listadoTareas = document.querySelector('#listado-tareas');
        // listadoTareas.innerHTML = ''; //limpia el codigo html pero requiere mas recursos

        while(listadoTareas.firstChild){ //mientras hay almenos un elemento hijo
            listadoTareas.removeChild(listadoTareas.firstChild); //elimina el primer elemento hijo
        }
    }


})();  //Los parentesis forzan a que el script se ejecute de manera inmediata
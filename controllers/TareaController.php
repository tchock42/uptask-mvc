<?php
namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController{
   public static function index(){
      session_start();
      $proyectoId = $_GET['id']; //realmente es url del proyecto

      if(!$proyectoId){
         header('location: /dashboard');
      }
      //validar que la url exista y sea de la sesión abierta
      $proyecto = Proyecto::where('url', $proyectoId); //busca en la tabla proyectos, en la columna url el valor de la url
      if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){ //si no hay resultado en la consulta o no coincide
         header('Location: /404');
      }
      //trae los proyectos del usuari con la sesion abierta
      $tareas = Tarea::belongsTo('proyectoId', $proyecto->id); // columna proyectoId valor id del proyecto
      echo json_encode(['tareas' => $tareas]);

   }
   public static function crear(){ //manda la información a javascript

      if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
         session_start(); //se inicia la sesion
         //$proyectoId = $_POST['proyectoId']; 
         //se busca en la tabla proyectos el proyecto que contiene el proyectoId
         $proyecto = Proyecto::where('url', $_POST['proyectoId']); //columna url, valor leido en el querystring

         if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){ //si no hay una respuesta a la consulta
            $respuesta = [
               'tipo' => 'error',
               'mensaje' => 'Hubo un error al agregar la tarea'
            ];
            echo json_encode($respuesta);
            return; //sale del metodo
         }//return termina el metodo
         
         //Todo bien, la consulta correcta, el usuarip y el proyectoId coinciden
         //Instanciar y crear la tarea
         $tarea = new Tarea($_POST); //instancia que contiene el nombre de la tarea y la url
         $tarea->proyectoId = $proyecto->id; //agrega el id del proyecto y quita la url
         $resultado = $tarea->guardar(); //retorna el resultado y el id
         $respuesta= [
            'tipo' =>  'exito', //tipo de alerta
            'id' => $resultado['id'],  //id de la tarea
            'mensaje' => 'Tarea creada correctamente', //mensaje de la alerta
            'proyectoId' => $proyecto->id //id del proyecto, no la url
         ]; 
         echo json_encode($respuesta);
      }
   }
   public static function actualizar (){

      if($_SERVER['REQUEST_METHOD'] === 'POST'){
         //validar que el proyecto exista
         $proyecto = Proyecto::where('url', $_POST['proyectoId']); //busca en url la url
         session_start();
         if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){ //si no hay una respuesta a la consulta
            $respuesta = [
               'tipo' => 'error',
               'mensaje' => 'Hubo un error al agregar la tarea'
            ];
            echo json_encode($respuesta);
            return; //sale del metodo
         }//return termina el metodo

         $tarea = new Tarea($_POST); //se crea el objeto tarea
         $tarea->proyectoId = $proyecto->id; //se cambia la url por el id del proyecto

         $resultado = $tarea->guardar();

         if($resultado){ //se prepara la respuesta de la api
            $respuesta = [
               'tipo' => 'exito',
               'id' => $tarea->id,  //id de la tarea
               'proyectoId' => $proyecto->id, //id del proyecto, no la url
               'mensaje' => 'Actualizado Correctamente'
            ];
            echo json_encode(['respuesta' => $respuesta]);
         }
         
         
         
      }
   }
     
   public static function eliminar(){
      //validar que el proyecto exista
      if($_SERVER['REQUEST_METHOD'] === 'POST'){
         $proyecto = Proyecto::where('url', $_POST['proyectoId']); //busca en url la url
         session_start(); //iniciar la sesion
         if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){ //si no hay una respuesta a la consulta
            $respuesta = [
               'tipo' => 'error',
               'mensaje' => 'Hubo un error al agregar la tarea'
            ];
            echo json_encode($respuesta);
            return; //sale del metodo
         }//return termina el metodo
         $tarea = new Tarea($_POST);
         $resultado = $tarea->eliminar();
         $resultado = [
            'resultado' => $resultado,
            'mensaje' => 'Eliminado  correctamente',
            'tipo' => 'exito'
         ];
         echo json_encode($resultado); 

      }
   }
}
<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController{
    public static function index(Router $router){
        session_start();
        isAuth();
        $id = $_SESSION['id'];
        
        $proyectos = Proyecto::belongsTo('propietarioId', $id);
        // debuguear($proyectos);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }
    public static function crear_proyecto(Router $router){
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $proyecto = new Proyecto($_POST);
            
            //validación
            $alertas = $proyecto->validarProyecto();
            if(empty($alertas)){
                //generar una url única
                $hash = md5(uniqid()); //genera un valor único y lo hashea con md5
                $proyecto->url = $hash;
                //almacenar el creardor del Proyecto
                $proyecto->propietarioId = $_SESSION['id'];
                // debuguear($proyecto);
                //Guardar el PRoyecto
                $proyecto->guardar();
                //Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }
    public static function proyecto(Router $router){
        session_start();
        isAuth(); 
        $alertas = [];
        $token = $_GET['id']; //este token es la url del proyecto
        if(!$token) header('Location: /dashboard');
        //revisar que la persona que visita el proyecto, es quien lo creó
        $proyecto = Proyecto::where('url', $token); //busca en la columna url

        if($proyecto->propietarioId !== $_SESSION['id']){
            header('Location: /dashboard');
        }
        // debuguear($proyecto);

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto, //se pasa titulo como el nombre traído por where
            'alertas' => $alertas
        ]);
    }

    public static function perfil(Router $router){
        session_start(); //inicia sesion
        isAuth(); //verifica que esté autenticado
        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']); //busca en la bd al usuario de la sesión
        // debuguear($usuario);
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST); //sincroniza lo que se escribe en los inputs
            $alertas = $usuario->validar_perfil();
            
            if(empty($alertas)){ //si no hay errores
                $existeUsuario = Usuario::where('email', $usuario->email); //busca el email de post
                // debuguear($existeUsuario); 
                if($existeUsuario && $existeUsuario->id !== $usuario->id){ //si existe un usuario con ese correo y tienen diferentes ids, mostrar error
                    //Mensaje de error
                    Usuario::setAlerta('error', 'Ya existe usuario asociado a este correo');
                    $alertas = $usuario->getAlertas();

                }else{ //si no hay un usuario con ese correo
                    //guardar el usuario
                    $usuario->guardar();
                    //actualizar la sesion
                    $_SESSION['nombre'] = $usuario->nombre;
                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    $alertas = $usuario->getAlertas();
                }
            }
        }

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    public static function cambiar_password(Router $router){
        session_start();
        isAuth();
        $alertas = [];
        //se detecta el usuario que quiere cambiar su password
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = Usuario::find($_SESSION['id']);

            //sincronizar con los dtos del usuario
            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevo_password(); //retorna las alertas
            
            if(empty($alertas)){
                $resultado = $usuario->comprobar_password();
                if($resultado){ //si password_actual coincide con la contraseña hasheada
                    $usuario->password = $usuario->password_nuevo; //copia en la instancia el password_nuevo
                    //eliminarpropiedades no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);
                    //hashear
                    $usuario->hashPassword();
                    //guardar contraseña (actualiza)
                    $resultado = $usuario->guardar(); //retorna resultado
                    if($resultado){
                        Usuario::setAlerta('exito', 'Contraseña guardada correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                
                }else{ //la contraseña actual no es correcta
                    Usuario::setAlerta('error', 'Contraseña incorrecta');
                    $alertas = $usuario->getAlertas();
                }
            }
        }
        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Contraseña',
            'alertas' => $alertas
        ]);
    }
}
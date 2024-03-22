<?php
namespace Controllers;

use Classes\Email;
use MVC\Router;
use Model\Usuario;

class  LoginController{
    public static function login( Router $router ){
        $alertas = [];
        // $usuario = new Usuario;
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();
            if(empty($alertas)){
                //verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email); //tambien se puede llamar usuario
                // debuguear($usuario);
                if(!$usuario || !$usuario->confirmado){ //si no hay un usuario con ese correo o no está confirmado
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                }else{
                    //el usuario existe y está confirmado
                    if( password_verify($_POST['password'], $usuario->password)){
                        //iniciar la sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        header('Location: /dashboard');
                    }else{
                        Usuario::setAlerta('error', 'Contraseña incorrecta, intenta otra vez');
                    }
                }
                // debuguear($usuario);
            }
        }
        $alertas = Usuario::getAlertas();
        //render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar sesión',
            'alertas' => $alertas
            // 'usuario' => $usuario
        ]);
    }
    public static function logout(){ //la sesion se cierra solo visitando una pagina, no requiere revisar el post
        session_start(); //inicia la sesion
        $_SESSION = []; //vacía el arreglo de session
        header('Location: /');  //redirecciona
    }

    public static function crear( Router $router){
        $alertas = [];
        $usuario = new Usuario; //crea un objeto vacío para que imprima en el value del formulario
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST); //sincroniza los valores del formmulario a $usuario
            // debuguear($usuario);
            $alertas = $usuario->validarNuevaCuenta();
            // debuguear($alertas);
            if(empty($alertas)){ //verifica que no haya alertas
                //Verifica si existe el usuario
                $existeUsuario = Usuario::where('email', $usuario->email); //si no existe, retorna null
                if($existeUsuario){ //si ya existe usuario con el correo registrado
                    Usuario::setAlerta('error', 'El Usuario ya está registrado'); //registra en memoria la alerta
                    $alertas = Usuario::getAlertas();
                }else{
                    //hashear el password
                    $usuario->hashPassword();

                    //elliminar password2
                    unset($usuario->password2);

                    //generar token
                    $usuario->crearToken();
                    //genera la instancia de Email para enviar el correo para confirmacion
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    // debuguear($email);
                    $email->enviarConfirmacion();

                    //crear un nuevo usuario
                    $resultado = $usuario->guardar();
                    if($resultado){
                        header('Location: /mensaje');
                    }
                    
                }
            }
        }
        //render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crear cuenta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    public static function olvide(Router $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();
            if(empty($alertas)){
                //Buscar al usuario 
                $usuario = Usuario::where('email', $usuario->email);
                if($usuario && $usuario->confirmado === "1"){
                    //Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);
                    //actualizar el usuario
                    $usuario->guardar();
                    //Enviar la alerta
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarInstrucciones();
                    // debuguear($email);
                    //imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                    // debuguear($usuario);
                }else{
                    Usuario::setAlerta('error', 'El Usuario no existe o no está confirmado');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide', [
            'titulo' => 'Recuperar contraseña',
            'alertas' => $alertas
        ]);

    }
    public static function reestablecer(Router $router){
        $alertas=[];
        $mostrar=true;
        $token = s($_GET['token']);
        // debuguear($token);
        if(!$token) header('Location: /');

        //encontrar al usuario con ese token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            //añadir el nuevo password
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;   
        }
        // debuguear($usuario);
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //añadir la nueva contraseña
            $usuario->sincronizar($_POST);
            //validar  la contraseña
            $alertas = $usuario->validarPassword();
            // debuguear($usuario);
            if(empty($alertas)){
                //hashear el nuevo passowrd
                $usuario->hashPassword();
                
                //eliminar el token
                $usuario->token = null; 
                //guardar el usuario en la base de datos
                $resultado = $usuario->guardar();
                //redireccionar
                if($resultado){
                    header('Location: /');
                }
            }
        }
        $alertas=Usuario::getAlertas();
        //Muestra la vista
        $router->render('auth/reestablecer', [
            'titulo'=> 'Escribe contraseña',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }
    public static function mensaje(Router $router){
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada Exitosamente'
        ]);
    }
    public static function confirmar(Router $router){
        $alertas = [];
        $token = s($_GET['token']);
        if(!$token) header('Location: /');
        // debuguear($token);
        //Encontrar al usuario con el token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            //Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no válido');
        }else{
            //Confirmar la cuenta
            $usuario->confirmado = 1; //confirma al usuario
            $usuario->token = null;
            unset($usuario->password2);
            //guardar en la base de datos
            // debuguear($usuario);
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }

        $alertas = Usuario::getAlertas();
        // debuguear($alertas);
        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta UpTask',
            'alertas' => $alertas
        ]);
    }
}
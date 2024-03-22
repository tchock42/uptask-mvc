<?php 

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\LoginController;
use Controllers\DashboardController;
use Controllers\TareaController;

$router = new Router();
//Login
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

//crear cuenta
$router->get('/crear', [LoginController::class, 'crear']); //pagina para crear cuenta
$router->post('/crear', [LoginController::class, 'crear']); //procesa la información

//Formulario de olvidé password
$router->get('/olvide', [LoginController::class, 'olvide']); //pagina por si se olvida la contraseña
$router->post('/olvide', [LoginController::class, 'olvide']); //procesa la información

//Colocar el nuevo password
$router->get('/reestablecer', [LoginController::class, 'reestablecer']); //pagina para pedir nuevo password
$router->post('/reestablecer', [LoginController::class, 'reestablecer']); //procesa la información

//confirmación de cuenta
$router->get('/mensaje', [LoginController::class, 'mensaje']); //pagina que visualiza revisar nuestro correo
$router->get('/confirmar', [LoginController::class, 'confirmar']); //con visitar la pagina se confrma la cuenta

//Zona de proyectos
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/proyecto', [DashboardController::class, 'proyecto']);
$router->get('/crear-proyecto', [DashboardController::class, 'crear_proyecto']);
$router->post('/crear-proyecto', [DashboardController::class, 'crear_proyecto']);
$router->get('/perfil', [DashboardController::class, 'perfil']);
$router->post('/perfil', [DashboardController::class, 'perfil']);
$router->get('/cambiar-password', [DashboardController::class, 'cambiar_password']);
$router->post('/cambiar-password', [DashboardController::class, 'cambiar_password']);
//API para las tareas - Estas paginas no se ven
$router->get('/api/tareas',[TareaController::class, 'index']); //muestra las tareas del proyecto
$router->post('/api/tarea', [TareaController::class, 'crear']); //nueva tarea
$router->post('/api/tarea/actualizar', [TareaController::class, 'actualizar']); //cambiar el estado de una tarea o renombrarlo
$router->post('/api/tarea/eliminar', [TareaController::class, 'eliminar']); //eliminar la tarea

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
<?php 

// Conectarnos a la base de datos
use Model\ActiveRecord;
require __DIR__ . '/../vendor/autoload.php';        //carga el autoload
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);  //extrae la propiedad dotenv del autoload
$dotenv->safeLoad();                                //carga el .env

require 'funciones.php';
require 'database.php';



ActiveRecord::setDB($db);
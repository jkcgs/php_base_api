<?php defined("INCLUDED") or die;
// Inicialización del sistema

header("Content-Type: application/json");
require "includes/functions.php";

@$config = include "includes/config.php";
if (!$config) {
    throw_error("El archivo de configuración no existe o no se pudo cargar.");
}

if($config['maintenance']) {
    throw_error("Sistema en mantención.");
}

// Carga las dependencias de composer
if (file_exists("composer.json") && @!include("vendor/autoload.php")) {
    throw_error(
        "No existe el archivo de autocarga de dependencias. " .
        "¿Ejecutaste 'composer install' en la carpeta 'api' de la aplicación?"
    );
}

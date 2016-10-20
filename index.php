<?php
// API base JSON
// Controla el backend del sistema y conecta con los módulos del API
// mediante ruteo de la url.

// Se define un 'flag' para que no se pueda cargar un archivo directamente
define("INCLUDED", true);

// Inicialización del sistema
require "includes/init.php";


//// AUTORUTEO ////
// Cuando algunos argumentos (separados por "/") no se detectan como una ruta existente,
// se pasan como argumentos de la ruta, para no tener que usar, por ejemplo, "?ruta/accion&id=1"
// entonces pasaría a ser "?ruta/accion/1", en donde el arreglo $route_args contendría ["1"]
// Adicionalmente, si no se especifica la acción, se extiende como "index", por ejemplo
// si tenemos la ruta "?ruta", y existe la acción ruta/index, entonces se agrega automáticamente
// Por último, se agregan automáticamente en el nivel de la acción los archivos index.php de la
// carpeta del módulo, nivel por nivel.
// Por ejemplo, para la ruta "ruta1/subruta1/accion", se incluyen automáticamente los archivos
// ruta1/index.php y ruta1/subruta1/index.php, en caso de existir. Esto no puede ser desactivado.

reset($_GET);
$route = key($_GET);

if($route != preg_replace("/[^a-zA-Z0-9_\-\/]+/", "", $route)) {
    throw_error("API no encontrada");
}

$route_exp = explode("/", trim($route, "/"));
$route_args = [];
$route_path = "";

// Generar ruta y argumentos
foreach ($route_exp as $arg) {
    if (is_dir("modules/$route_path/$arg")) {
        array_shift($route_exp);
        if (empty($route_path)) {
            $route_path .= $arg;
        } else {
            $route_path .= "/$arg";
        }
    } elseif (is_file("modules/$route_path/$arg.php")) {
        $route_path = "$route_path/$arg.php";
        array_shift($route_exp);
    } else {
        $route_args = $route_exp;
    }
}

if (is_file("modules/$route_path/index.php")) {
    $route_path = "$route_path/index.php";
} elseif (empty($route_path)) {
    throw_error("Ruta de API no ingresada");
} elseif (is_dir("modules/$route_path")) {
    throw_error("API no encontrada");
}

// Incluye el módulo solicitado y sus súpermódulos (index.php en cada carpeta)
try {
    $current_mod = "";

    $fpaths = explode("/", $route_path);
    $fcurrent = "";
    foreach ($fpaths as $fpath) {
        $fcurrent .= "/" . $fpath;
        $current_mod = "modules$fcurrent/index.php";

        if(is_file($current_mod)) {
            require_once $current_mod;
        }
    }

    $current_mod = "modules/$route_path";
    require $current_mod;
} catch(\Exception $e) {
    // Generar un error cuando se ha producido un error en un módulo
    header("HTTP/1.1 500 Internal Server Error");
    throw_error(
        "Se ha producido un error al intentar cargar un módulo: \n".
        ($current_mod ? "Módulo: $current_mod\n" : "") .
        $e->getMessage() . "\n" . $e->getTraceAsString()
    );
}
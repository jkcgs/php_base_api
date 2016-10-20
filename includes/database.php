<?php defined("INCLUDED") or die("Denied");
// Se proveen varias funciones para manejar la base de datos.
// Incluir este script realizará la conexión con la base de datos
// y expondrá la variable "$db" con dicha conexión.

if(!isset($config)) {
    $config = include dirname(__FILE__) . "/../includes/config.php";
}

// Se muestra en el mensaje de error cuando se utilizó configuración por defecto
$db_defaults = false;

if(empty($config['db_host'])) {
    $config['db_host'] = "localhost";
    $db_defaults = true;
}

if(empty($config['db_user']) == "") {
    $config['db_user'] = "root";
    $db_defaults = true;
}

if(empty($config['db_name'])) {
    throw_error("No se ha definido el nombre de la base de datos");
    exit;
}

$db_error = false;
@$db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass']);
$def_info = $db_defaults ? " - Se utilizó configuración por defecto." : "";

// Renombrar errores
switch ($db->connect_errno) {
	case null:
        // No hubo error
		break;

	case 1045:
		throw_error("No se pudo conectar al servidor MySQL: Permiso denegado (usando contraseña)" . $def_info);
		break;
	
	default:
        if($config['development']) {
            throw_error("Fallo al conectar a MySQL: #{$db->connect_errno} {$db->connect_error}" . $def_info);
        } else {
            throw_error("No fué posible conectarse a la base de datos " . $def_info);
        }

		break;
}

if(!$db->select_db($config['db_name'])) {
    throw_error("No se pudo seleccionar la base de datos");
    exit;
}

function db_fetch_all($q) {
    $r = array();
    while ($row = $q->fetch_assoc()) {
	    $r[] = $row;
	}

    return $r;
}

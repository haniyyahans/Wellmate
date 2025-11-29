<?php

// Set error reporting untuk dev, kalo udah digabungin ini hpus aja
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get controller dan method dari URL
$c = $_GET['c'] ?? 'Tracking'; // Default controller
$m = $_GET['m'] ?? 'index';    // Default method

// Load base controller, mewarisi fungsi induk
require_once("controller/Controller.class.php");

// Load specific controller
$controllerFile = "controller/$c.class.php";
if (file_exists($controllerFile)) {
    require_once($controllerFile);
} else {
    die("Controller $c tidak ditemukan");
}

// membuat objek dari controller tsb
if (class_exists($c)) {
    $controller = new $c();
} else {
    die("Class $c tidak ditemukan");
}

// Execute method
if (method_exists($controller, $m)) {
    $controller->$m();
} else {
    // Fallback ke index jika method tidak ada
    if (method_exists($controller, 'index')) {
        $controller->index();
    } else {
        die("Method $m tidak ditemukan di controller $c");
    }
}
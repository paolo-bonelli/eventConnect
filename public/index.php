<?php

// Require config and core libraries
require_once '../config/config.php';
require_once '../core/Controller.php';

// Create core controller
$app = new Controller();

// Default route
$app->view('main/header');
$app->view('home/index');

?>
<?php

// Define the root directory of the application (one level above public)
define('ROOT', dirname(__DIR__));
define('HOME', '/eventConnect/public/');

// Require config and core libraries
require_once ROOT . '/config/config.php';
require_once ROOT . '/core/Controller.php';

// Create core controller
$app = new Controller();
$appState = State::getInstance();

// Example routing logic
$requestedRoute = $_SERVER['REQUEST_URI'];
$routes = [
    HOME => 'home/index',
    HOME . 'events/' => 'events/index',
    // ... other routes
];

$app->view('main/header', $data=array('title' => $appState->getState()['title']));


if (array_key_exists($requestedRoute, $routes)) {
    $app->view($routes[$requestedRoute]);
    // Call the appropriate controller and method
    // ...
} else {
    // No route found, display the 404 page
    http_response_code(404);
    $app->view('main/404');
    exit;
}

$app->view('main/footer')
?>
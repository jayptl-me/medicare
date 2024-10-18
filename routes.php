<?php
require_once 'vendor/autoload.php';

use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;

$router = new RouteCollector(new Std(), new GroupCountBased());

$router->addRoute('GET', '/register', function () {
  require_once __DIR__ . '/public/auth/register.php';
});

$router->addRoute('POST', '/register', function () {
  require_once __DIR__ . '/public/auth/register.php';
});

$router->addRoute('GET', '/', function () {
  require_once __DIR__ . '/public/pages/home.php';
});

$router->addRoute('GET', '/home', function () {
  require_once __DIR__ . '/public/pages/home.php';
});

$router->addRoute('GET', '/logout', function () {
  require_once __DIR__ . '/public/auth/logout.php';
});

// Updated route for the schedule page
$router->addRoute('GET', '/schedule', function () {
  require_once __DIR__ . '/public/pages/schedule.php';
});

$router->addRoute('POST', '/schedule', function () {
  require_once __DIR__ . '/public/pages/schedule.php';
});

$dispatcher = new GroupCountBasedDispatcher($router->getData());

$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        call_user_func($handler, $vars);
        break;
}
<?php
session_start();
include '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/..");
$dotenv->load();

if (!isset($_SESSION['isAuth'])) {
    $_SESSION['isAuth'] = false;
    $_SESSION['isAdmin'] = false;
    $_SESSION['level'] = 0;
}

$url = explode('/', $_SERVER['REQUEST_URI']);

if(!empty($url[1])) {
    $segment = $url[1];
    $controller = 'app\Http\Controllers\\'.ucfirst($url[1]).'Controller';
    $method = filter_var($url[2], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
} else {
    $segment = 'home';
    $controller = 'app\Http\Controllers\HomeController';
    $method = 'index';
}

//$routes = include '../routes/web.php';
//
//foreach ($routes[$_SESSION['level']] as $key => $value) {
//    if(isset($routes[$_SESSION['level']][$key][$segment]) && $routes[$_SESSION['level']][$key][$segment] == $method) {
        if(class_exists($controller)) {
            $rClass = new ReflectionClass($controller);
            if($rClass->isInstantiable()) {
                $class = new $controller();
                $u = array_slice($url, 3);
                if($rClass->hasMethod($method)){
                    $rMethod = new ReflectionMethod($controller, $method);
                    if($rMethod->isPublic()) {
                        $class->{$method}(...$u);
                        die();
                    }
                }
            }
//        }
//    }
}

header('HTTP/1.0 Page Not Found');

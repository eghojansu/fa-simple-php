<?php

use app\Helper;
use app\Request;
use app\Response;

// bootstrap
require 'app/bootstrap.php';

// current path, underscore removed, and its an absolute path
$current_path = $app->service(Request::class)->currentPath(true);
    $current_path = str_replace('_', '', $current_path);
    $current_path = Helper::ensureAbsolute($current_path);

// handling request
$namespace = 'app\\module\\';
$suffix = 'Controller';
$defaultController = 'Index'.$suffix;
$defaultMethod = 'main';
$errorHandler = $namespace.'Error'.$suffix;
$segments = explode('/', $current_path);
    $segments = array_filter($segments);
if ($segments) {
    // handle it, support two-depth controller location
    if (class_exists($class = $namespace.$segments[0].$suffix)
        || class_exists($class = $namespace.$segments[0].'\\'.$segments[0].$suffix)
    ) {
        array_shift($segments);
        $method = $segments?array_shift($segments):$defaultMethod;
    } elseif (isset($segments[1]) && class_exists($class = $namespace.$segments[0].'\\'.$segments[1].$suffix)) {
        array_shift($segments);
        array_shift($segments);
        $method = $segments?array_shift($segments):$defaultMethod;
    } else {
        $class = $errorHandler;
        $method = 'notFound';
    }
    $args = $segments;
} else {
    // use default
    $class = $namespace.$defaultController;
    $method = $defaultMethod;
    $args = [];
}

// fix method
if (false === method_exists($class, $method)) {
    array_unshift($args, $method);
    $method = $defaultMethod;
}

// check method visibility
$mref = new ReflectionMethod($class, $method);
if (false === $mref->isPublic()) {
    // invalid method
    $class = $errorHandler;
    $method = 'notAllowed';
}

// controller construction
// construct
$instance = $app->service($class);
$response = true;
if (method_exists($instance, 'beforeRoute')) {
    $response = $app->call($instance, 'beforeRoute', $args);
}
if (true === $response || null === $response) {
    $response = $app->call($instance, $method, $args);
}
if (method_exists($instance, 'afterRoute')) {
    $response = $app->call($instance, 'afterRoute', $args);
}

if ($response instanceof Response) {
    // send response
    $response->send();
} else {
    echo "Invalid response!";
}

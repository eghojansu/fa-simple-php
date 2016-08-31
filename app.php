<?php

use app\core\Helper;
use app\core\Request;
use app\core\Response;

// bootstrap
require 'app/bootstrap.php';

// current path
$current_path      = $app->service(Request::class)->currentPath(true);

// handling request
$namespace         = $app->get('controllerNamespace');
$suffix            = $app->get('controllerSuffix');
$defaultController = $app->get('controllerDefault');
$defaultMethod     = $app->get('controllerDefaultMethod');
$errorHandler      = $app->get('controllerError');
$segments          = array_filter(explode('/', $current_path));

// default
$class             = $defaultController;
$method            = $defaultMethod;
$args              = [];

if ($segments) {
    $str = $namespace;
    $class = $errorHandler;
    $method = 'notFound';
    foreach ($segments as $key=>$segment) {
        $segment = Helper::fixRouteToClassMap($segment);
        if (class_exists($c = $str.ucfirst($segment).$suffix) ||
            class_exists($c = $str.$segment.'\\'.ucfirst($segment).$suffix)) {
            $class = $c;
            $segments = array_slice($segments, $key+1);
            $method = $segments?Helper::fixRouteToClassMap(array_shift($segments)):$defaultMethod;
            $args = $segments;

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
            break;
        }

        $str .= $segment.'\\';
    }
}

// controller construction
$instance = $app->service($class);
$response = null;
if (method_exists($instance, 'beforeRoute')) {
    $response = $app->call($instance, 'beforeRoute', $args);
}
if (false !== $response) {
    $response = $app->call($instance, $method, $args);
}
// if (method_exists($instance, 'afterRoute')) {
//     $app->call($instance, 'afterRoute', $args);
// }

if ($response instanceof Response) {
    // send response
    $response->send();
} else {
    echo "Invalid response! please fix it";
}

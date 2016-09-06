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
    $class = $errorHandler;
    $method = 'notFound';
    $pMethod = $defaultMethod;
    while ($segments) {
        $clone = $segments;
        $pClass = ucfirst(Helper::fixRouteToClassMap(array_pop($clone)));
        $pNamespace = $clone ? Helper::fixRouteToClassMap(implode('\\', $clone).'\\') : '';
        if (class_exists($c = $namespace.$pNamespace.$pClass.$suffix)
            || class_exists($c = $namespace.$pNamespace.lcfirst($pClass).'\\'.$pClass.$suffix)
        ) {
            $class = $c;

             // check method
            if (method_exists($class, $pMethod)) {
                $method = $pMethod;
                array_shift($args);
            }
            else {
                $method = $defaultMethod;
            }

            // check method visibility
            $mref = new ReflectionMethod($class, $method);
            if (false === $mref->isPublic() || '_' === $method[0] || method_exists($class, '_'.$method)) {
                // invalid method
                $class = $errorHandler;
                $method = 'notAllowed';
            }
            $mref = null;

            $segments = [];
        }
        else {
            $last = array_pop($segments);
            $pMethod = Helper::fixRouteToClassMap($last);
            array_unshift($args, $last);
        }
    }
}
unset($current_path, $namespace, $suffix, $defaultController,
    $defaultMethod, $errorHandler, $segments, $pMethod, $clone,
    $pClass, $pNamespace, $mref);

// controller construction
$instance = $app->service($class);
$response = null;
if (method_exists($instance, '_beforeRoute')) {
    $response = $app->call($instance, '_beforeRoute', $args);
}
if (false === $response || null === $response) {
    $response = $app->call($instance, $method, $args);
}

if ($response instanceof Response) {
    // send response
    $response->send();
} else {
    echo "Invalid response! please fix it";
}

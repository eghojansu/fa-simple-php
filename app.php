<?php

// autoloader class
require 'app/autoload.php';

// instantiate main class
$app = App::instance();

// register configuration from file
$config = $app->load('app/config/config.php');
$app->register($config);

// register services
$config = $app->load('app/config/services.php');
$app->registerServices($config);

// set default page title
// by copying application name to pageTitle variable
$app->copy('name', 'pageTitle');

// module path
$module_path   = $app->get('modulePath');
$template_path = $app->get('templatePath');
// extension
$extension     = '.php';
// current path
$current_path  = $app->service('request')->currentPath(true);
// replace extension
$current_path  = preg_replace('/'.$extension.'$/', '', $current_path);
// remove underscore
$current_path  = ltrim($current_path, '_');
// ensure this is absolute path
$current_path  = Helper::ensureAbsolute($current_path);
// path to load, if current path exists use it otherwise use default (index page)
// then, if that path exists assume that path should be served with index page
// otherwise use plain path and concat with extension
$path          = $module_path.($current_path?:'index');
$path          = (file_exists($path)?$path.'/index':$path).$extension;
// not found file
$error404      = $template_path.'not-found'.$extension;
// file to load
$fileToLoad    = is_file($path)?$path:$error404;

// set current path
$app->set('currentPath', $current_path);

// load main file
ob_start();
require $fileToLoad;
$app->set('content', ob_get_clean());

// load template if exists
if ($template = $app->get('template')) {
    ob_start();
    require $template_path.$template.$extension;
    $app->set('content', ob_get_clean());
}

// set content respond then send
$app->service('response')
    ->setContent($app->cut('content'))
    ->send()
;
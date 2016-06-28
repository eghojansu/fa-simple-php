<?php

// load main class
require 'app/autoload.php';

// instantiate main class
$app = new App;

// register configuration from file
$app->load('app/config/config.php');

// set default page title
// by copying application name to pageTitle variable
$app->copy('name', 'pageTitle');

// module path
$module_path   = __DIR__.'/app/modules/';
$template_path = __DIR__.'/app/template/';
// extension
$extension     = '.php';
// current path
$current_path  = str_replace($extension, '', $app->service->get('request')->currentPath());
// path to load
$path          = $module_path.($current_path?:'index');
$path          = (file_exists($path)?$path.'/index':$path).$extension;
// not found file
$error404      = $template_path.'not-found'.$extension;
// file to load
$fileToLoad    = is_file($path)?$path:$error404;

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
$app->service->get('response')
    ->setContent($app->cut('content'))
    ->send()
;
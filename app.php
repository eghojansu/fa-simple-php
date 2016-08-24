<?php

// bootstrap
require 'app/bootstrap.php';

// set default page title
// by copying application name to pageTitle variable
$app->copy('name', 'pageTitle');

// expose some object
$user = $app->service('user');
$request = $app->service('request');
$response = $app->service('response');

// module path
$module_path   = $app->get('modulePath');
$template_path = $app->get('templatePath');
// extension
$extension     = '.php';
// current path
$current_path  = $request->currentPath(true);
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
// set template based on user type
if ($user->hasBeenLogin() && ($role = $user->get('role')) && file_exists($template_path.$role.$extension)) {
    $app->set('template', $role);
}

// load main file, catch its content
ob_start();
require $fileToLoad;
$app->set('content', ob_get_clean());

// load template if exists
if ($template = $app->get('template')) {
    ob_start();
    require $template_path.$template.$extension;
    $app->set('content', ob_get_clean());
}

// set content response then send
$response
    ->setContent($app->cut('content'))
    ->send()
;

<?php

// autoloader class
require 'app/autoload.php';

// instantiate main class
$app = new App;

// set base path
$app->set('base', realpath(__DIR__).'/');

// register configuration from file
$config = $app->load('app/config/config.php');
$app->register($config);

// register services
$config = $app->load('app/config/services.php');
$app->registerServices($config);

// database manipulation
$user = $app->service('user');
if (!$user->exists('db_created')) {
    $db = $app->service('database');
    $db
        ->drop()
        ->create()
        ->import('app/schema/1 schema.sql')
        ->import('app/schema/2 user-init.sql');
    for ($i=0; $i < 100; $i++) {
        $data['name'] = 'User '.$i;
        $data['username'] = 'user-'.$i;
        $data['password'] = $data['username'];
        $db->insert('user', $data);
    }
    $user->set('db_created', true);
}
// end database manipulation

// set default page title
// by copying application name to pageTitle variable
$app->copy('name', 'pageTitle');

// module path
$module_path   = __DIR__.'/app/modules/';
$template_path = __DIR__.'/app/template/';
// extension
$extension     = '.php';
// current path
$current_path  = str_replace($extension, '', $app->service('request')->currentPath());
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
    // nav only used in template for default
    $app->set('nav', $app->load('app/config/nav.php'));
    ob_start();
    require $template_path.$template.$extension;
    $app->set('content', ob_get_clean());
}

// set content respond then send
$app->service('response')
    ->setContent($app->cut('content'))
    ->send()
;
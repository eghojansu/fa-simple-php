<?php

use app\Model;
use app\core\App;
use app\core\Breadcrumb;
use app\core\Database;
use app\core\Loader;

// require loader
require __DIR__.'/core/Loader.php';

$loader = new Loader;
$loader
    ->add(dirname(__DIR__))
    ->register()
;

// instantiate main class
$app = App::instance();

// register configuration from file
// it will remains in App properties
$config = $app->load(__DIR__.'/config/config.php');
$app->register($config);

// configure services
$database = $app->load(__DIR__.'/config/database.php');
$rules = [
    Database::class => [
        'constructParams'=>[$database],
        'shared'=>true,
    ],
    Model::class => [
        'shared'=>true,
    ],
    // Breadcrumb::class => [
    //     'shared'=>true,
    //     'constructParams'=>[
    //      '<svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg>',
    //      'beranda',
    //     ],
    // ],
];
$service = $app->service();
foreach ($rules as $key => $value) {
    $service->addRule($key, $value);
}

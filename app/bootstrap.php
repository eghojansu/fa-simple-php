<?php

// require loader
require __DIR__.'/Loader.php';

$loader = new Loader;
$loader
    ->add(__DIR__)
    ->register()
;

// instantiate main class
$app = App::instance();

// register configuration from file
$config = $app->load(__DIR__.'/config/config.php');
$app->register($config);

$database = $app->load(__DIR__.'/config/database.php');
$rules = [
    'database' => [
        'instanceOf'=>'Database',
        'constructParams'=>[$database],
        'shared'=>true,
    ],
    // 'breadcrumb' => [
    //     'instanceOf'=>'Breadcrumb',
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

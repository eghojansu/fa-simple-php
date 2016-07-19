<?php

$database = require('database.php');

return [
    'database' => [
        'instanceOf'=>'Database',
        'constructParams'=>[$database],
        'shared'=>true,
    ],
    // 'model' => [
    //     'instanceOf'=>'Model',
    //     'shared'=>true,
    // ],
    // 'breadcrumb' => [
    //     'instanceOf'=>'Breadcrumb',
    //     'shared'=>true,
    //     'constructParams'=>[
    //     	'<svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg>',
    //     	'beranda',
    //     ],
    // ],
];
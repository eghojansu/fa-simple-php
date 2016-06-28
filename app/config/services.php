<?php

$database = require('database.php');

return [
    'database' => [
        'instanceOf'=>'Database',
        'constructParams'=>[$database],
        'shared'=>true,
    ],
];
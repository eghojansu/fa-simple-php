<?php

$dirs = require(__DIR__.'/config/autoload.php');
$dirs = array_merge([__DIR__.'/'], $dirs);

spl_autoload_register(function($class) use ($dirs) {
    $file = str_replace('\\', '/', $class);
    $ext  = '.php';
    foreach ($dirs as $dir) {
        if (is_readable($f = $file.$ext)
            || is_readable($f = $dir.$file.$ext)
            || is_readable($f = $dir.strtolower($file).$ext)) {
            require $f;
        }
    }
});
<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$homeUrl = 'master/user';
$filter = [
    'id = ?',
    $app->service('request')->query('id')
];
$app->service('database')->delete('user', $filter);
$user->message('success', 'Data sudah dihapus!');
$app->service('response')->redirect($homeUrl);
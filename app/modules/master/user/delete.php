<?php

$user = $app->service->get('user');
$user->mustLogin()->orRedirect('index');

$homeUrl = 'master/user';
$filter = [
    'id = ?',
    $app->service->get('request')->query('id')
];
$app->service->get('database')->delete('user', $filter);
$user->message('success', 'Data sudah dihapus!');
$app->service->get('response')->redirect($homeUrl);
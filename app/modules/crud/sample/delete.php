<?php

$user = $app->service->get('user');
$user->mustLogin()->orRedirect('index');

$homeUrl = 'crud/sample';
$filter = [
    'id_warga = ?',
    $app->service->get('request')->query('id')
];
/*
$app->service->get('database')->delete('table', $filter);
*/
$user->message('success', 'Data sudah dihapus!');
$app->service->get('response')->redirect($homeUrl);
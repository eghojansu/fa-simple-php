<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$homeUrl = $app->urlPath(__DIR__);
$filter = [
    'id = ? and id <> ?',
    $app->service('request')->query('id'),
    $user->get('id'),
];
$db = $app->service('database');
$response = $app->service('response');

$db->delete('user', $filter);
$user->message('info', 'Data sudah dihapus!');
$response->redirect($homeUrl);
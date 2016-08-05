<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$request = $app->service('request');
$response = $app->service('response');
$db = $app->service('database');

$homeUrl = $app->urlPath(__DIR__);
$filter = [
    'id = ? and id <> ?',
    $request->query('id'),
    $user->get('id'),
];

$db->delete('user', $filter);
$user->message('info', 'Data sudah dihapus!');
$response->redirect($homeUrl);
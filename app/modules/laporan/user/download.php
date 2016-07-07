<?php

$user = $app->service->get('user');
$user->mustLogin()->orRedirect('index');

$request = $app->service->get('request');
$filter = [];
if ($keyword = $request->query('keyword')) {
    $filter = [
        '(name like :keyword or username like :keyword)',
        ':keyword' => '%'.$keyword.'%'
    ];
}

$data = $app->service->get('database')->select('name,username,level', 'user', $filter);
$header = ['Name','Username','Level'];
Helper::sendCSV('data-user', $header, $data);
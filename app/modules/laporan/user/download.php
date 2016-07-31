<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$request = $app->service('request');
$db = $app->service('database');
$filter = [];
if ($keyword = $request->query('keyword')) {
    $filter = [
        '(name like :keyword or username like :keyword)',
        ':keyword' => '%'.$keyword.'%'
    ];
}

$data = $db->select('name,username,level', 'user', $filter);
$header = ['Name','Username','Level'];
Helper::sendCSV('data-user', $header, $data);
<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$db = $app->service('database');
$request = $app->service('request');
$response = $app->service('response');
$homeUrl = $app->urlPath(__DIR__);
$error = null;
$filter = [
    'id = ? and id <> ?',
    $request->query('id'),
    $user->get('id'),
];
$record = $db->findOne('user', $filter);
if (!$record) {
    $user->message('warning', 'Data tidak ditemukan');
    $response->redirect($homeUrl);
}
$fields = [
    'name'=>$request->get('name', $record['name']),
    'username'=>$request->get('username', $record['username']),
    'password'=>$request->get('password', $record['password']),
    'level'=>$request->get('level', $record['level']),
];

$labels = $app->load('app/config/translations/user-labels.php');
if ($request->isPost()) {
    $rules = [
        'name,username,password'=>'required',
    ];
    $validation = $app->service('validation', [$fields, $rules, $labels]);
    $error = $validation->validate()->getError();

    if (!$error) {
        $saved = $db->update('user', $fields, $filter);
        if ($saved) {
            $user->message('success', 'Data sudah disimpan!');
            $response->redirect($homeUrl);
        }
         else {
            $error = 'Data gagal disimpan!';
        }
    }
    $user->message('error', $error);
}

$form = $app->service('form')
  ->setData($fields)
  ->setLabels($labels)
  ->setAttrs([
    'class'=>'form-horizontal',
  ])
  ->setDefaultControlAttrs([
    'class'=>'form-control',
  ])
  ->setDefaultLabelAttrs([
    'class'=>'form-label col-md-2',
  ])
;

$app->set('currentPath', $homeUrl);
?>
<h1 class="page-header">
    Data User
    <small>edit</small>
</h1>

<?php
include __DIR__.'/_form.php';
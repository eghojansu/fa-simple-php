<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$request = $app->service('request');
$homeUrl = $app->urlPath(__DIR__);
$fields = [
    'name'=>$request->get('name'),
    'username'=>$request->get('username'),
    'password'=>$request->get('password'),
    'level'=>$request->get('level'),
];
$error = null;

$labels = $app->load('app/config/translations/user-labels.php');
if ($request->isPost()) {
    $db = $app->service('database');
    $rules = [
        'name,username,password'=>'required',
    ];
    $validation = $app->service('validation', [$fields, $rules, $labels]);
    $error = $validation->validate()->getError();

    if (!$error) {
        $saved = $db->insert('user', $fields);
        if ($saved) {
            $user->message('success', 'Data sudah disimpan!');
            $response = $app->service('response');
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
    <small>baru</small>
</h1>

<?php
include __DIR__.'/_form.php';
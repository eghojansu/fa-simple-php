<?php

$user = $app->service('user');
$response = $app->service('response');

$user->logout();
$response->redirect('account/login');
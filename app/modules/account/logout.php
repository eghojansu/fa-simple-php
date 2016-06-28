<?php

$app->service->get('user')->logout();
$app->service->get('response')->redirect('account/login');
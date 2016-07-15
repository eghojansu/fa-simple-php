<?php

$app->service('user')->logout();
$app->service('response')->redirect('account/login');
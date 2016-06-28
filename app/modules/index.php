<?php

$user = $app->service->get('user');
$user->mustLogin()->orRedirect('account/login');

?>

<h1 class="page-header">Welcome, <?php echo $user->get('nama'); ?></h1>
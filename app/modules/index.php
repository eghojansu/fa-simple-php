<?php

if ($user->isAnonym()) {
    $response->redirect('account/login');
}

?>

<h1 class="page-header">Welcome, <?php echo $user->get('name'); ?></h1>

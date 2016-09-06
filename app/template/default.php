<?php
$html = $this->html;
$user = $this->user;
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle; ?></title>
    <link href="<?php echo $this->app->asset('asset/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $this->app->asset('asset/css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo $this->app->asset('asset/css/bootstrap-datepicker3.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $this->app->asset('asset/css/daterangepicker.css'); ?>" rel="stylesheet">
    <link href="<?php echo $this->app->asset('asset/css/style.css'); ?>" rel="stylesheet">

    <script src="<?php echo $this->app->asset('asset/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo $this->app->asset('asset/js/moment.min.js'); ?>"></script>
    <script src="<?php echo $this->app->asset('asset/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo $this->app->asset('asset/js/bootstrap-datepicker.min.js'); ?>"></script>
    <script src="<?php echo $this->app->asset('asset/js/bootstrap-datepicker.id.min.js'); ?>"></script>
    <script src="<?php echo $this->app->asset('asset/js/daterangepicker.js'); ?>"></script>
    <script src="<?php echo $this->app->asset('asset/js/notify.min.js'); ?>"></script>
    <script src="<?php echo $this->app->asset('asset/js/bootbox.min.js'); ?>"></script>
    <script src="<?php echo $this->app->asset('asset/js/autonumeric.js'); ?>"></script>
    <script src="<?php echo $this->app->asset('asset/js/script.js'); ?>"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

    <div class="container">
      <div class="jumbotron">
        <h1>This is a landing page</h1>
        <?php if ($user->is('admin')): ?>
          <a href="<?php echo $this->app->url('admin'); ?>">Beranda</a>
        <?php else: ?>
          <a href="<?php echo $this->app->url('admin/login'); ?>">Login Admin</a>
        <?php endif; ?>
      </div>
    </div>

    <?php
      echo $html->notify('success', $user->message('success'));
      echo $html->notify('error', $user->message('error'));
      echo $html->notify('warning', $user->message('warning'));
      echo $html->notify('info', $user->message('info'));
    ?>
  </body>
</html>

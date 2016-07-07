<?php
$html = $app->service->get('html');
$nav  = $app->get('nav');
$currentPath = $app->get('currentPath');
$main_nav = $html->navbarNav($nav['main'], $currentPath);
$account_nav = $html->navbarNav($nav['account'], $currentPath, ['appendClass'=>'navbar-right']);
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $app->get('pageTitle'); ?></title>
    <link href="<?php echo $app->asset('public/css/bootstrap.united-theme.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $app->asset('public/css/bootstrap-datepicker3.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $app->asset('public/css/style.css'); ?>" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><?php echo $app->get('alias'); ?></a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <?php echo $main_nav; ?>
          <?php echo $account_nav; ?>
        </div>
      </div>
    </nav>

    <div class="container">
      <?php echo $app->get('content'); ?>
    </div>

    <script src="<?php echo $app->asset('public/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo $app->asset('public/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo $app->asset('public/js/bootstrap-datepicker.min.js'); ?>"></script>
    <script src="<?php echo $app->asset('public/js/bootstrap-datepicker.id.min.js'); ?>"></script>
    <script src="<?php echo $app->asset('public/js/notify.min.js'); ?>"></script>
    <script src="<?php echo $app->asset('public/js/bootbox.min.js'); ?>"></script>
    <script src="<?php echo $app->asset('public/js/script.js'); ?>"></script>
  </body>
</html>
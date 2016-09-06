<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    <link href="<?php echo $this->app->asset('asset/css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="<?php echo $this->app->asset('asset/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $this->app->asset('asset/css/style.css'); ?>" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container container-error">
      <div class="well">
        <h1><?php echo $title; ?></h1>
        <hr>
        <p><?php echo $message; ?></p>
      </div>
    </div>
  </body>
</html>

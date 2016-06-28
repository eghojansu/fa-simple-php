<?php

$user = $app->service->get('user');
$user->mustAnonym()->orRedirect('index');
$app->clear('template');

$request  = $app->service->get('request');
$username = $request->get('username');
$password = $request->get('password');
$error    = null;

if ($request->isPost()) {
  $data = ['username'=>$username,'nama'=>$username,'password'=>$password];
  /*
  $db = $app->service->get('database');
  $filter = [
    'username = ? and password = ?',
    $username,
    $password,
  ];
  $data = $db->findOne('user', $filter);
  */

  if (empty($data)) {
    $error = 'Login gagal! ';//.$db->getError();
  } else {
    $user->login('user', $data);
    $app->service->get('response')->redirect('index');
  }
}
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="<?php echo $app->asset('public/css/bootstrap.united-theme.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $app->asset('public/css/style.css'); ?>" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="fillbg"><img src="<?php echo $app->asset('public/images/bg.jpg'); ?>"></div>
    <div class="container-login">
      <form method="post">
        <h1 class="page-title">Login</h1>
        <?php if ($error): ?>
          <div class="alert alert-danger">
            <?php echo $error; ?>
          </div>
        <?php endif; ?>
        <div class="form-group">
          <label for="username" class="sr-only">Username</label>
          <input type="text" name="username" value="<?php echo $username; ?>" class="form-control form-block" placeholder="username">
        </div>
        <div class="form-group">
          <label for="password" class="sr-only">Password</label>
          <input type="password" name="password" class="form-control form-block" placeholder="password">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
      </form>
    </div>
  </body>
</html>
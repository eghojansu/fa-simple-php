<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$request      = $app->service('request');
$username     = $request->get('username', $user->get('username'));
$password     = $request->get('password', $user->get('password'));
$new_password = $request->get('new_password');
$name         = $request->get('name', $user->get('name'));
$error        = null;

if ($request->isPost()) {
  $old_password = $user->get('password');
  $data = [
    'username' => $username,
    'password' => $password,
    'new_password' => $new_password,
    'name' => $name,
  ];
  $rules = [
    'name,username'=>'required',
    'password'=>'required,Password saat ini tidak boleh kosong',
    '-password'=>"equal($old_password),Password saat ini tidak valid",
    'new_password'=>'minLength(4,allowEmpty)',
  ];
  $error = $app->service('validation', [$data, $rules])->validate()->getError();
  unset($data['new_password']);
  if ($new_password) {
    $data['password'] = $new_password;
  }
  // handle file
  $filename = $app->get('base').'public/avatars/user-'.$user->get('id');
  if (Helper::handleFileUpload('avatar', $filename, ['image/jpeg','image/jpg','image/png'])) {
    $data['avatar'] = basename($filename);
  }

  if (!$error) {
    $db = $app->service('database');
    $filter = [
      'id = ?',
      $user->get('id'),
    ];
    $saved = $db->update('user', $data, $filter);
    if ($saved) {
      $user->register($data);
      $user->message('success', 'Data sudah diupdate');
      $app->service('response')->redirect('account/profil');
    }
    else {
      $error = 'Data gagal disimpan!'.$db->getError();
    }
  }
}

$avatar = $user->get('avatar');
$avatar = $app->asset($avatar?'public/avatars/'.$avatar:'public/images/avatar.png');

$html = $app->service('html');
echo $html->notify('error', $error);
echo $html->notify('success', $user->message('success'));
?>
<h1 class="page-header">Profile</h1>

<div class="row">
  <div class="col-md-5">
    <form method="post" class="form-horizontal" enctype="multipart/form-data">
      <div class="form-group">
        <label for="password" class="col-md-4 form-label">Password saat ini</label>
        <div class="col-md-8">
          <input type="password" name="password" class="form-control" placeholder="password">
        </div>
      </div>
      <hr>
      <div class="form-group">
        <label for="name" class="col-md-4 form-label">Nama</label>
        <div class="col-md-8">
          <input type="text" name="name" value="<?php echo $name; ?>" class="form-control" placeholder="name">
        </div>
      </div>
      <hr>
      <div class="form-group">
        <label for="username" class="col-md-4 form-label">Username</label>
        <div class="col-md-8">
          <input type="text" name="username" value="<?php echo $username; ?>" class="form-control" placeholder="username">
        </div>
      </div>
      <hr>
      <div class="form-group">
        <label for="avatar" class="col-md-4 form-label">Avatar</label>
        <div class="col-md-8">
          <input type="file" name="avatar" class="form-control">
        </div>
      </div>
      <hr>
      <div class="form-group">
        <label for="new_password" class="col-md-4 form-label">Password Baru</label>
        <div class="col-md-8">
          <input type="password" name="new_password" class="form-control" placeholder="kosongkan jika tidak ingin mengubah password">
        </div>
      </div>
      <hr>
      <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
          <button type="submit" class="btn btn-primary">Login</button>
          <a href="<?php echo $app->url('index'); ?>" class="btn btn-default">Batal</a>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-7">
    <strong>Avatar :</strong>
    <img src="<?php echo $avatar; ?>" class="thumbnail" height="160px">
  </div>
</div>
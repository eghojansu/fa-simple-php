<?php

$user = $app->service->get('user');
$user->mustLogin()->orRedirect('index');

$request      = $app->service->get('request');
$username     = $request->get('username', $user->get('username'));
$password     = $request->get('password', $user->get('password'));
$new_password = $request->get('new_password');
$nama         = $request->get('nama', $user->get('nama'));
$error        = null;

if ($request->isPost()) {
  $old_password = $user->get('password');
  $data = [
    'username' => $username,
    'password' => $password,
    'new_password' => $new_password,
    'nama' => $nama,
  ];
  $rules = [
    'nama,username,password'=>'required',
    'password'=>"equal($old_password,allowEmpty),Password tidak valid",
    'new_password'=>'minLength(4,allowEmpty)',
  ];
  $error = $app->service->get('validation', [$data, $rules])->validate()->getError();
  unset($data['new_password']);
  if ($new_password) {
    $data['password'] = $new_password;
  }

  if (!$error) {
    $saved = true;
    /*
    $db = $app->service->get('database');
    $filter = [
      'id_user = ?',
      $user->get('id'),
    ];
    $saved = $db->update('user', $data, $filter);
    */
    if ($saved) {
      $user->register($data);
      $user->message('success', 'Data sudah diupdate');
      $app->service->get('response')->redirect('account/profil');
    }
    else {
      $error = 'Data gagal disimpan!';//.$db->getError();
    }
  }
}

$html = $app->service->get('html');
echo $html->notify('error', $error);
echo $html->notify('success', $user->message('success'));
?>
<h1 class="page-header">Profile</h1>

<form method="post" class="form-horizontal">
  <div class="form-group">
    <label for="password" class="col-md-2 form-label">Password saat ini</label>
    <div class="col-md-4">
      <input type="password" name="password" class="form-control" placeholder="password">
    </div>
  </div>
  <hr>
  <div class="form-group">
    <label for="nama" class="col-md-2 form-label">Nama</label>
    <div class="col-md-4">
      <input type="text" name="nama" value="<?php echo $nama; ?>" class="form-control" placeholder="nama">
    </div>
  </div>
  <hr>
  <div class="form-group">
    <label for="username" class="col-md-2 form-label">Username</label>
    <div class="col-md-4">
      <input type="text" name="username" value="<?php echo $username; ?>" class="form-control" placeholder="username">
    </div>
  </div>
  <hr>
  <div class="form-group">
    <label for="new_password" class="col-md-2 form-label">Password Baru</label>
    <div class="col-md-4">
      <input type="password" name="new_password" class="form-control" placeholder="kosongkan jika tidak ingin mengubah password">
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-10 col-md-offset-2">
      <button type="submit" class="btn btn-primary">Login</button>
      <a href="<?php echo $app->url('index'); ?>" class="btn btn-default">Batal</a>
    </div>
  </div>
</form>
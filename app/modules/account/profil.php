<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$request  = $app->service('request');
$response = $app->service('response');
$db       = $app->service('database');
$fields = [
  'username'=>$request->get('username', $user->get('username')),
  'password'=>$request->get('password', $user->get('password')),
  'new_password'=>$request->get('new_password', $user->get('new_password')),
  'name'=>$request->get('name', $user->get('name')),
];
$error   = null;
$selfUrl = $app->urlPath(__FILE__);

if ($request->isPost()) {
  $old_password = $user->get('password');
  $rules = [
    'name,username'=>'required',
    'password'=>'required,Password saat ini tidak boleh kosong',
    '-password'=>"equal($old_password),Password saat ini tidak valid",
    'new_password'=>'minLength(4,allowEmpty)',
  ];
  $error = $app->service('validation', [$fields, $rules])->validate()->getError();

  if (!$error) {
    // handle file
    $filename = $request->baseDir().'public/avatars/user-'.$user->get('id');
    if (Helper::handleFileUpload('avatar', $filename, $app->get('imageTypes'))) {
      $fields['avatar'] = basename($filename);
    }

    if ($fields['new_password']) {
      $fields['password'] = $fields['new_password'];
    }
    unset($fields['new_password']);

    $filter = [
      'id = ?',
      $user->get('id'),
    ];
    $saved = $db->update('user', $fields, $filter);
    if ($saved) {
      $user->register($fields);
      $user->message('success', 'Data sudah diupdate');
      $response->redirect($selfUrl);
    }
    else {
      $error = 'Data gagal disimpan!'.$db->getError();
    }
  }
}

$avatar = $user->get('avatar');
$avatar = $app->asset($avatar?'public/avatars/'.$avatar:'public/images/avatar.png');

$form = $app->service('form', [$fields,[
  'class'=>'form-horizontal',
  'enctype'=>'multipart/form-data'
  ]]);
$form->setDefaultControlAttrs([
  'class'=>'form-control',
  ]);

$html = $app->service('html');
echo $html->notify('error', $error);
echo $html->notify('success', $user->message('success'));
?>
<h1 class="page-header">Profile</h1>

<div class="row">
  <div class="col-md-5">
    <?php echo $form->open(); ?>
      <div class="form-group">
        <label for="password" class="col-md-4 form-label">Password saat ini</label>
        <div class="col-md-8">
          <?php echo $form->password('password',['value'=>null]); ?>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <label for="name" class="col-md-4 form-label">Nama</label>
        <div class="col-md-8">
          <?php echo $form->text('name'); ?>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <label for="username" class="col-md-4 form-label">Username</label>
        <div class="col-md-8">
          <?php echo $form->text('username'); ?>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <label for="avatar" class="col-md-4 form-label">Avatar</label>
        <div class="col-md-8">
          <?php echo $form->file('avatar'); ?>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <label for="new_password" class="col-md-4 form-label">Password Baru</label>
        <div class="col-md-8">
          <?php echo $form->password('new_password', ['placeholder'=>'kosongkan jika tidak ingin mengubah password']); ?>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="<?php echo $app->url('index'); ?>" class="btn btn-default">Batal</a>
        </div>
      </div>
    </form>
    <?php echo $form->close(); ?>
  </div>
  <div class="col-md-7">
    <strong>Avatar :</strong>
    <img src="<?php echo $avatar; ?>" class="thumbnail" height="160px">
  </div>
</div>
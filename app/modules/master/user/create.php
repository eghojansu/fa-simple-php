<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$db = $app->service('database');
$request = $app->service('request');
$homeUrl = $app->urlPath(__DIR__);
$fields = [
    'name'=>$request->get('name'),
    'username'=>$request->get('username'),
    'password'=>$request->get('password'),
    'level'=>$request->get('level'),
];
$error = null;
$filter = [
    'id = ? and id <> ?',
    $request->query('id'),
    $user->get('id'),
];

if ($request->isPost()) {
    $rules = [
        'name,username,password'=>'required',
    ];
    $error = $app->service('validation', [$fields, $rules])->validate()->getError();

    if (!$error) {
        $saved = $db->insert('user', $fields);
        if ($saved) {
            $user->message('success', 'Data sudah disimpan!');
            $app->service('response')->redirect($homeUrl);
        }
         else {
            $error = 'Data gagal disimpan!'.$db->getError();
        }
    }
}

$form = $app->service('form', [$fields,[
    'class'=>'form-horizontal'
]]);
$form->setDefaultControlAttrs([
    'class'=>'form-control',
    ]);

$html = $app->service('html');
echo $html->notify('error', $error);

$app->set('currentPath', $homeUrl);
?>
<h1 class="page-header">
    Data User
    <small>input</small>
</h1>

<?php echo $form->open(); ?>
    <div class="form-group">
        <label for="name" class="form-label col-md-2">Name</label>
        <div class="col-md-4">
            <?php echo $form->text('name'); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="username" class="form-label col-md-2">Username</label>
        <div class="col-md-4">
            <?php echo $form->text('username'); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="form-label col-md-2">Password</label>
        <div class="col-md-4">
            <?php echo $form->password('password'); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="level" class="form-label col-md-2">Level</label>
        <div class="col-md-4">
            <?php echo $form->select('level',['options'=>$app->get('userLevel')]); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo $app->url($homeUrl); ?>" class="btn btn-default">Batal</a>
        </div>
    </div>
</form>
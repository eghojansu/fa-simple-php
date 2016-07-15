<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$request = $app->service('request');
$homeUrl = 'master/user';
$fields = [
    'name'=>$request->get('name'),
    'username'=>$request->get('username'),
    'password'=>$request->get('password'),
    'level'=>$request->get('level'),
];
$error = null;
$filter = [
    'id = ?',
    $request->query('id')
];
$db = $app->service('database');
$record = $db->findOne('user', $filter);
$found = !empty($record);
$record += $fields;

if ($request->isPost()) {
    $rules = [
        'name,username,password'=>'required',
    ];
    $error = $app->service('validation', [$fields, $rules])->validate()->getError();

    if (!$error) {
        $saved = $found?
            // update
            $db->update('user', $fields, $filter) :
            // insert
            $db->insert('user', $fields);
        if ($saved) {
            $user->message('success', 'Data sudah disimpan!');
            $app->service('response')->redirect($homeUrl);
        }
         else {
            $error = 'Data gagal disimpan!'.$db->getError();
        }
    }
}

$html = $app->service('html');
echo $html->notify('error', $error);
?>
<h1 class="page-header">
    Data User
    <small>input</small>
</h1>

<form method="post" class="form-horizontal">
    <div class="form-group">
        <label for="name" class="form-label col-md-2">Name</label>
        <div class="col-md-4">
            <input type="text" name="name" placeholder="name" value="<?php echo $record['name']; ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label for="username" class="form-label col-md-2">Username</label>
        <div class="col-md-4">
            <input type="text" name="username" placeholder="username" value="<?php echo $record['username']; ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="form-label col-md-2">Password</label>
        <div class="col-md-4">
            <input type="password" name="password" placeholder="password" value="<?php echo $record['password']; ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label for="level" class="form-label col-md-2">Level</label>
        <div class="col-md-4">
            <select name="level" class="form-control">
                <?php foreach ($app->get('userLevel') as $level): ?>
                    <option value="<?php echo $level; ?>" <?php echo $level===$record['level']?'selected':null; ?>><?php echo $level; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo $app->url($homeUrl); ?>" class="btn btn-default">Batal</a>
        </div>
    </div>
</form>
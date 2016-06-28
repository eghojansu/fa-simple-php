<?php

$user = $app->service->get('user');
$user->mustLogin()->orRedirect('index');

$request = $app->service->get('request');
$homeUrl = 'crud/sample';
$fields = [
    'columna'=>$request->get('columna'),
    'columnb'=>$request->get('columnb', date('Y-m-d')),
    'columnc'=>$request->get('columnc'),
];
$error = null;
$filter = [
    'id_warga = ?',
    $request->query('id')
];
$record = [];
/*
$db = $app->service->get('database');
$record = $db->findOne('table', $filter);
*/
$found = !empty($record);
$record += $fields;

if ($request->isPost()) {
    $rules = [
        'columna,columnb,columnc'=>'required',
    ];
    $error = $app->service->get('validation', [$fields, $rules])->validate()->getError();

    if (!$error) {
        $saved = true;
        /*
        $saved = $found?
            // update
            $db->update('table', $fields, $filter) :
            // insert
            $db->insert('table', $fields);
        */
        if ($saved) {
            $user->message('success', 'Data sudah disimpan!');
            $app->service->get('response')->redirect($homeUrl);
        }
         else {
            $error = 'Data gagal disimpan!';//.$db->getError();
        }
    }
}

$html = $app->service->get('html');
echo $html->notify('error', $error);
?>
<h1 class="page-header">
    Data Sample
    <small>input</small>
</h1>

<form method="post" class="form-horizontal">
    <div class="form-group">
        <label for="columna" class="form-label col-md-2">Column A</label>
        <div class="col-md-4">
            <input type="text" name="columna" placeholder="columna" value="<?php echo $record['columna']; ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label for="columnb" class="form-label col-md-2">Column B</label>
        <div class="col-md-2">
            <input type="text" name="columnb" placeholder="columnb" value="<?php echo $record['columnb']; ?>" class="form-control" data-toggle="datepicker">
        </div>
    </div>
    <div class="form-group">
        <label for="columnc" class="form-label col-md-2">Column C</label>
        <div class="col-md-4">
            <input type="text" name="columnc" placeholder="columnc" value="<?php echo $record['columnc']; ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo $app->url($homeUrl); ?>" class="btn btn-default">Batal</a>
        </div>
    </div>
</form>
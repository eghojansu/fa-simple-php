<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

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
$db = $app->service('database');
$record = $db->findOne('user', $filter);
$found = !empty($record);
$record += $fields;

if ($request->isPost()) {
    $rules = [
        'name,username,password'=>'required',
        '-name'=>function($value, $field, Validation $validation) {
            $pattern = '/^user/i';
            if (!preg_match($pattern, $value)) {
                $validation->setError($field, $value, 'Nama harus diawali user');
            }
        },
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

$form = $app->service('form', [$record,[
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
        <label for="exampleRadio" class="form-label col-md-2">Example Radio</label>
        <div class="col-md-4">
            <?php echo $form->radio('exampleRadio',['class'=>'','label'=>'Radio Single'],true); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="exampleCheckbox" class="form-label col-md-2">Example Checkbox</label>
        <div class="col-md-4">
            <?php echo $form->checkbox('exampleCheckbox',['class'=>'','value'=>'notchecekd'],true); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="exampleRadioWrapped" class="form-label col-md-2">Example RadioWrapped</label>
        <div class="col-md-4">
            <?php echo $form->radio('exampleRadioWrapped',['class'=>'','label'=>'RadioWrapped Single','wrapLabel'=>true],true); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="exampleCheckboxWrapped" class="form-label col-md-2">Example CheckboxWrapped</label>
        <div class="col-md-4">
            <?php echo $form->checkbox('exampleCheckboxWrapped',['class'=>'','wrapLabel'=>true],true); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="exampleRadioList" class="form-label col-md-2">Example RadioList</label>
        <div class="col-md-4">
            <?php echo $form->radioList('exampleRadioList',['class'=>'','options'=>$app->get('userLevel'),'wrapLabel'=>['class'=>'radio-inline']],true); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="exampleCheckboxList" class="form-label col-md-2">Example CheckboxList</label>
        <div class="col-md-4">
            <?php echo $form->checkboxList('exampleCheckboxList',['class'=>'','options'=>$app->get('userLevel'),'wrapLabel'=>['class'=>'checkbox-inline']],true); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="exampleTextarea" class="form-label col-md-2">Textarea</label>
        <div class="col-md-4">
            <?php echo $form->textarea('acd'); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="monthList" class="form-label col-md-2">monthList</label>
        <div class="col-md-4">
            <?php echo $form->monthList('monthList',['placeholder'=>'pilih bulan']); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="numberList" class="form-label col-md-2">numberList</label>
        <div class="col-md-4">
            <?php echo $form->numberList('numberList',['placeholder'=>'tahun','start'=>2016,'end'=>2020]); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="dateList" class="form-label col-md-2">dateList</label>
        <div class="col-md-4">
            <?php echo $form->dateList('dateList',[]); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo $app->url($homeUrl); ?>" class="btn btn-default">Batal</a>
        </div>
    </div>
</form>
<?php

$user = $app->service->get('user');
$user->mustLogin()->orRedirect('index');

$homeUrl = 'crud/sample';
$filter = [
    'id_warga = ?',
    $app->service->get('request')->query('id')
];
$record = [
    'columna'=>'Value A',
    'columnb'=>'Value B',
    'columnc'=>'Value C',
];
/*
$record = Database::findOne('table', $filter);
*/
if (empty($record)) {
    $user->message('error', 'Data tidak ditemukan');
    $app->service->get('response')->redirect($homeUrl);
}
?>
<h1 class="page-header">
    Data Sample
    <small>detail</small>
</h1>

<table class="table">
    <tbody>
        <tr>
            <td colspan="3"><a href="<?php echo $app->url($homeUrl); ?>" data-toggle="tooltip" title="Kembali">&laquo;</a></td>
        </tr>
        <tr>
            <td style="width: 200px">Column A</td>
            <td style="width: 30px">:</td>
            <td><?php echo $record['columna']; ?></td>
        </tr>
        <tr>
            <td>Column B</td>
            <td>:</td>
            <td><?php echo $record['columb']; ?></td>
        </tr>
        <tr>
            <td>Column C</td>
            <td>:</td>
            <td><?php echo $record['columc']; ?></td>
        </tr>
    </tbody>
</table>
<?php

$user = $app->service('user');
$user->mustLogin()->orRedirect('index');

$homeUrl = 'master/user';
$filter = [
    'id = ?',
    $app->service('request')->query('id')
];
$record = $app->service('database')->findOne('user', $filter);
if (empty($record)) {
    $user->message('error', 'Data tidak ditemukan');
    $app->service('response')->redirect($homeUrl);
}
?>
<h1 class="page-header">
    Data User
    <small>detail</small>
</h1>

<table class="table">
    <tbody>
        <tr>
            <td colspan="3"><a href="<?php echo $app->url($homeUrl); ?>" data-toggle="tooltip" title="Kembali">&laquo;</a></td>
        </tr>
        <tr>
            <td style="width: 200px">Name</td>
            <td style="width: 30px">:</td>
            <td><?php echo $record['name']; ?></td>
        </tr>
        <tr>
            <td>Username</td>
            <td>:</td>
            <td><?php echo $record['username']; ?></td>
        </tr>
        <tr>
            <td>Level</td>
            <td>:</td>
            <td><?php echo $record['level']; ?></td>
        </tr>
    </tbody>
</table>
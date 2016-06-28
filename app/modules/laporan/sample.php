<?php

$user = $app->service->get('user');
$user->mustLogin()->orRedirect('index');

$request = $app->service->get('request');
$filter = [];
if ($keyword = $request->query('keyword')) {
    $filter = [
        '(nomor_ktp like :keyword or nama like :keyword)',
        ':keyword' => '%'.$keyword.'%'
    ];
}
$page = $request->query('page', 1);
$subset = ['count'=>0,'page'=>$page,'total'=>0];
/*
$subset = $app->service->get('database')->paginate('table', $filter, $page);
*/

$selfUrl = 'laporan/sample';

$html = $app->service->get('html');
echo $html->notify('success', $user->message('success'));
echo $html->notify('error', $user->message('error'));
?>
<h1 class="page-header">Laporan Sample</h1>

<?php echo $app->service->get('html')->pagination($subset, ['route'=>$selfUrl]); ?>

<form class="form-inline">
    <div class="form-group">
        <label for="keyword" class="sr-only">Keyword</label>
        <input type="text" name="keyword" class="form-control" value="<?php echo $keyword; ?>" placeholder="keyword">
    </div>
    <button type="submit" class="btn btn-info">Search</button>
</form>

<hr>

<table class="table table-bordered table-condensed table-striped table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>ColumnA</th>
            <th>ColumnB</th>
            <th>ColumnC</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($subset['count'] > 0): ?>
            <?php foreach ($subset['data'] as $row): ?>
                <tr>
                    <td><?php echo $subset['start']++; ?></td>
                    <td><?php echo $row['columna']; ?></td>
                    <td><?php echo $row['columnb']; ?></td>
                    <td><?php echo $row['columnc']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">tidak ada data</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
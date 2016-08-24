<?php

if ($user->isAnonym()) {
    $response->redirect('account/login');
}

$filter = [];
if ($keyword = $request->query('keyword')) {
    $filter = [
        '(name like :keyword or username like :keyword)',
        ':keyword' => '%'.$keyword.'%'
    ];
}
$page = $request->query('page', 1);
$subset = $app->service('database')->paginate('user', $filter, null, $page);

$homeUrl = $app->urlPath(__DIR__);
$printUrl = $homeUrl.'/print';
$downloadUrl = $homeUrl.'/download';

$html = $app->service('html');
$app->set('currentPath', $homeUrl);
?>
<h1 class="page-header">Laporan User</h1>

<?php echo $html->pagination($subset, ['route'=>$homeUrl]); ?>

<form class="form-inline">
    <div class="form-group">
        <label for="keyword" class="sr-only">Keyword</label>
        <input type="text" name="keyword" class="form-control" value="<?php echo $keyword; ?>" placeholder="name or username">
    </div>
    <button type="submit" class="btn btn-info">Search</button>
</form>
<br>
<hr>

<div class="btn-group pull-right" role="group">
    <a href="<?php echo $app->url($printUrl, $_GET); ?>" class="btn btn-info"><span class="glyphicon glyphicon-print"></span> Print</a>
    <a href="<?php echo $app->url($downloadUrl, $_GET); ?>" class="btn btn-warning"><span class="glyphicon glyphicon-download"></span> Download</a>
</div>

<br>
<br>
<br>

<table class="table table-bordered table-condensed table-striped table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Username</th>
            <th>Level</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($subset['data']): ?>
            <?php foreach ($subset['data'] as $row): ?>
                <tr>
                    <td><?php echo $subset['start']++; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['level']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">tidak ada data</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

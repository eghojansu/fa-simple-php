<?php

$user = $app->service->get('user');
$user->mustLogin()->orRedirect('index');

$request = $app->service->get('request');
$filter = [];
if ($keyword = $request->query('keyword')) {
    $filter = [
        '(name like :keyword or username like :keyword)',
        ':keyword' => '%'.$keyword.'%'
    ];
}
$page = $request->query('page', 1);
$subset = $app->service->get('database')->paginate('user', $filter, null, $page);
$selfUrl = 'master/user';
$inputUrl = 'master/user/input';
$deleteUrl = 'master/user/delete';
$detailUrl = 'master/user/detail';

$html = $app->service->get('html');
echo $html->notify('success', $user->message('success'));
echo $html->notify('error', $user->message('error'));
?>
<h1 class="page-header">Data User</h1>

<?php echo $html->pagination($subset, ['route'=>$selfUrl]); ?>

<form class="form-inline">
    <div class="form-group">
        <label for="keyword" class="sr-only">Keyword</label>
        <input type="text" name="keyword" class="form-control" value="<?php echo $keyword; ?>" placeholder="name or username">
    </div>
    <button type="submit" class="btn btn-info">Search</button>
</form>

<hr>

<table class="table table-bordered table-condensed table-striped table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Username</th>
            <th>Level</th>
            <th>
                <a class="text-primary" href="<?php echo $app->url($inputUrl); ?>" data-toggle="tooltip" title="Data baru"><span class="glyphicon glyphicon-plus-sign"></span></a>
            </th>
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
                    <td>
                        <a class="text-warning" href="<?php echo $app->url($detailUrl, ['id'=>$row['id']]); ?>" data-toggle="tooltip" title="Detail"><span class="glyphicon glyphicon-eye-open"></span></a>
                        <a class="text-success" href="<?php echo $app->url($inputUrl, ['id'=>$row['id']]); ?>" data-toggle="tooltip" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a>
                        <a data-confirm="delete" class="text-danger" href="<?php echo $app->url($deleteUrl, ['id'=>$row['id']]); ?>" data-toggle="tooltip" title="Delete"><span class="glyphicon glyphicon-remove-sign"></span></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">tidak ada data</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
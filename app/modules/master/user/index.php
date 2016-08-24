<?php

if ($user->isAnonym()) {
    $response->redirect('account/login');
}

$db = $app->service('database');
$filter = ['id <> :self', ':self'=>$user->get('id')];
if ($keyword = $request->query('keyword')) {
    $filter[0] .= ' and (name like :keyword or username like :keyword)';
    $filter[':keyword'] = '%'.$keyword.'%';
}
$page = $request->query('page', 1);
$subset = $db->paginate('user', $filter, null, $page);
$homeUrl = $app->urlPath(__DIR__);
$createUrl = $homeUrl.'/create';
$updateUrl = $homeUrl.'/update';
$deleteUrl = $homeUrl.'/delete';
$detailUrl = $homeUrl.'/detail';

$html = $app->service('html');
?>
<h1 class="page-header">Data User</h1>


<div class="data-control clearfix">
    <div class="btn-group pull-right">
        <a class="btn btn-primary" href="<?php echo $app->url($createUrl); ?>" data-toggle="tooltip" title="Data baru"><i class="fa fa-pencil-square-o"></i> Data Baru</a>
    </div>

    <form class="form-inline">
        <div class="form-group">
            <label for="keyword" class="sr-only">Keyword</label>
            <input type="text" name="keyword" class="form-control" value="<?php echo $keyword; ?>" placeholder="name or username">
        </div>
        <button type="submit" class="btn btn-info">Search</button>
    </form>
</div>
<?php echo $html->pagination($subset, ['route'=>$homeUrl]); ?>


<table class="table table-bordered table-condensed table-striped table-hover first-no last-control">
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Username</th>
            <th>Level</th>
            <th></th>
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
                        <a class="btn btn-xs btn-warning" href="<?php echo $app->url($detailUrl, ['id'=>$row['id']]); ?>" data-toggle="tooltip" title="Detail"><i class="fa fa-info"></i></a>
                        <a class="btn btn-xs btn-success" href="<?php echo $app->url($updateUrl, ['id'=>$row['id']]); ?>" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                        <a data-confirm="delete" class="btn btn-xs btn-danger" href="<?php echo $app->url($deleteUrl, ['id'=>$row['id']]); ?>" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>
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

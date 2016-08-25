<h1 class="page-header">Laporan User</h1>

<div class="data-control clearfix hidden-print">
    <div class="btn-group pull-right" role="group">
        <a href="<?php echo $this->app->url($homeUrl, $_GET); ?>" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
        <button class="btn btn-info" onclick="window.print()"><span class="glyphicon glyphicon-print"></span> Print</button>
    </div>
</div>

<table class="table table-bordered table-condensed table-striped table-hover table-print">
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Username</th>
            <th>Level</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($data): ?>
            <?php $no = 1; foreach ($data as $row): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
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

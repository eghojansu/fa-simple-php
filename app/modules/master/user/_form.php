<?php echo $form->open(); ?>
    <div class="form-group">
        <?php echo $form->label('name'); ?>
        <div class="col-md-4">
            <?php echo $form->text('name'); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $form->label('username'); ?>
        <div class="col-md-4">
            <?php echo $form->text('username'); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $form->label('password'); ?>
        <div class="col-md-4">
            <?php echo $form->password('password'); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $form->label('level'); ?>
        <div class="col-md-4">
            <?php echo $form->select('level',['options'=>$app->get('userLevel')]); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?php echo $app->url($homeUrl); ?>" class="btn btn-default">Batal</a>
        </div>
    </div>
<?php echo $form->close(); ?>
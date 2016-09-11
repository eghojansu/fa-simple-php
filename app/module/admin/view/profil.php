<h1 class="page-header">Profile</h1>

<div class="row">
  <div class="col-md-5">
    <?php echo $form->open(); ?>
      <div class="form-group">
        <?php echo $form->label('password'); ?>
        <div class="col-md-8">
          <?php echo $form->password('password',['value'=>null]); ?>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <?php echo $form->label('name'); ?>
        <div class="col-md-8">
          <?php echo $form->text('name'); ?>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <?php echo $form->label('username'); ?>
        <div class="col-md-8">
          <?php echo $form->text('username'); ?>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <?php echo $form->label('avatar'); ?>
        <div class="col-md-8">
          <?php echo $form->file('avatar'); ?>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <?php echo $form->label('password baru', ['for'=>'new_password']); ?>
        <div class="col-md-8">
          <?php echo $form->password('new_password', ['placeholder'=>'kosongkan jika tidak ingin mengubah password']); ?>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="<?php echo $app->url($backUrl); ?>" class="btn btn-default">Batal</a>
        </div>
      </div>
    </form>
    <?php echo $form->close(); ?>
  </div>
  <div class="col-md-7">
    <strong>Avatar :</strong>
    <img src="<?php echo $avatar; ?>" class="thumbnail" height="160px">
  </div>
</div>

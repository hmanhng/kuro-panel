<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-12">
        <?= $this->include('Layout/msgStatus') ?>
    </div>
    <div class="col-lg-6">
        <div class="card mb-3" style="background: linear-gradient(0.9turn, #ff0, #ebe, #00ffff); 80rem;">
            <div class="card-body h5 text-center text-dark">
                Change Password
            </div>
            <div class="card-body">
                <?= form_open() ?>

                <input type="hidden" name="password_form" value="1">
                <div class="form-group mb-2">
                    <label for="current">Current Password</label>
                    <input type="password" name="current" id="current" class="form-control mt-2 rounded-pill" placeholder="Current Password">
                    <?php if ($validation->hasError('current')) : ?>
                        <small id="help-current" class="text-danger"><?= $validation->getError('current') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group mb-2">
                    <label for="password">New Password</label>
                    <input type="password" name="password" id="password" class="form-control mt-2 rounded-pill" placeholder="New Password" aria-describedby="help-password">
                    <?php if ($validation->hasError('password')) : ?>
                        <small id="help-password" class="text-danger"><?= $validation->getError('password') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group mb-2">
                    <label for="password2">Confirm Password</label>
                    <input type="password" name="password2" id="password2" class="form-control mt-2 rounded-pill" placeholder="Password" aria-describedby="help-password2">
                    <?php if ($validation->hasError('password2')) : ?>
                        <small id="help-password2" class="text-danger"><?= $validation->getError('password2') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group my-2">
                    <button type="submit" class="btn btn-outline-primary rounded-pill">Change Password</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
     <div class="col-lg-6">
        <div class="card card bg-gradient-light shadow h-10 py-2 mb-3">
            <div class="card-header h5 p-3 text-dark text-center">
                Update Email
            </div>
            <div class="card-body">
                <?= form_open() ?>
                <input type="hidden" name="email_form" value="1">
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" class="form-control mt-2 rounded-pill" placeholder="" aria-describedby="help-fullname" value="<?= old('email') ?: ($user->email ?: '') ?>">
                    <?php if ($validation->hasError('email')) : ?>
                        <small id="help-email" class="text-danger"><?= $validation->getError('email') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group my-2">
                    <button type="submit" class="btn btn-outline-primary rounded-pill">Update Email</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
  </div>
    </div>
</div>
<?= $this->endSection() ?>
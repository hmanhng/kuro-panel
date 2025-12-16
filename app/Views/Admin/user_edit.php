<?php
include('mail.php');
?>

<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center pt-3">
    <div class="col-lg-8">
        <?= $this->include('Layout/msgStatus') ?>
    </div>
    <div class="col-lg-8 mb-3">
        <div class="card mb-5">
            <div class="card-header p-3 bg-dark text-white">
                Acc Info &middot; <?= getName($target) ?>
                <div class="col text-end">
                        <a class="btn btn-sm btn-outline-light" href="<?= site_url('admin/manage-users') ?>" class="py-1 px-2 bg-white text-muted"><i class="bi bi-arrow-left"></i></a>
                </div>
            </div>
            <div class="card-body">
                <?= form_open() ?>
                <input type="hidden" name="user_id" value="<?= $target->id_users ?>">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control rounded-pill" placeholder="" aria-describedby="help-username" value="<?= old('username') ?: $target->username ?>">
                        <?php if ($validation->hasError('username')) : ?>
                            <small id="help-username" class="form-text text-danger"><?= $validation->getError('username') ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="fullname" class="form-label">Fullname</label>
                        <input type="text" name="fullname" id="fullname" class="form-control rounded-pill" placeholder="" aria-describedby="help-fullname" value="<?= old('fullname') ?: $target->fullname ?>">
                        <?php if ($validation->hasError('fullname')) : ?>
                            <small id="help-fullname" class="form-text text-danger"><?= $validation->getError('fullname') ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="level" class="form-label">Roles</label>
                        <?php $sel_level = ['' => '&mdash; Select Roles &mdash;', '1' => 'Admin', '2' => 'Reseller',]; ?>
                        <?= form_dropdown(['class' => 'form-select rounded-pill', 'name' => 'level', 'id' => 'level'], $sel_level, $target->level) ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="status" class="form-label">Status</label>
                        <?php $sel_status = ['' => '&mdash; Select Status &mdash;', '2' => 'Banned/Block', '1' => 'Active', '0' => 'Expired']; ?>
                        <?= form_dropdown(['class' => 'form-select rounded-pill', 'name' => 'status', 'id' => 'status'], $sel_status, $target->status) ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="saldo" class="form-label">Saldo</label>
                        <input type="number" name="saldo" id="saldo" class="form-control rounded-pill" placeholder="" aria-describedby="help-saldo" value="<?= old('saldo') ?: $target->saldo ?>">
                        <?php if ($validation->hasError('saldo')) : ?>
                            <small id="help-saldo" class="form-text text-danger"><?= $validation->getError('saldo') ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="uplink" class="form-label">Uplink</label>
                        <input type="text" name="uplink" id="uplink" class="form-control rounded-pill" placeholder="" aria-describedby="help-uplink" value="<?= old('uplink') ?: $target->uplink ?>">
                        <?php if ($validation->hasError('uplink')) : ?>
                            <small id="help-uplink" class="form-text text-danger"><?= $validation->getError('uplink') ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6 mb-2">
                        <label for="expiration_date" class="form-label">Expiration</label>
                        <input type="text" name="expiration" id="expiration" class="form-control rounded-pill" placeholder="" aria-describedby="help-expiration_date" value="<?= old('expiration_date') ?: $target->expiration_date ?>">
                        <?php if ($validation->hasError('expiration_date')) : ?>
                            <small id="help-expiration_date" class="form-text text-danger"><?= $validation->getError('expiration_date') ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6 mb-2">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="email" id="email" class="form-control rounded-pill" placeholder="" aria-describedby="help-email" value="<?= old('email') ?: $target->email ?>">
                        <?php if ($validation->hasError('email')) : ?>
                            <small id="help-email" class="form-text text-danger"><?= $validation->getError('email') ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-12 mt-2">
                        <button type="submit" class="btn btn-outline-dark rounded-pill">Update Account Information</button>
                    </div>
                </div>
                <?= form_close() ?>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center pt-5">
    <div class="col-lg-4">
        <?= $this->include('Layout/msgStatus') ?>
        <div class="col-lg-12">
        <div class="alert alert-dark p-3 mb-4 text-dark" role="alert">
            <strong>INFO</strong> <i class="bi bi-caret-right-fill"></i> The owner of this panel is HMANHNG, so if anyone wants a reseller panel then
            <a href="https://telegram.me/hmanhng"><strong>Dm Me</strong></a>
        </div>
        <div class="card shadow p-3 mb-5">
            <div class="card-body h2 text-center">
                <div>
                    
                    Login</div>
                    </div>
            <div class="card-body">
                <?= form_open() ?>
                <div class="form-group mb-3">
                    <label for="username">Username</label>
                    <input type="text" class="form-control mt-2" name="username" id="username" aria-describedby="help-username" placeholder="Enter Your username" required minlength="4">
                    <?php if ($validation->hasError('username')) : ?>
                        <small id="help-username" class="form-text text-danger"><?= $validation->getError('username') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Password</label>
                    <input type="password" class="form-control mt-2" name="password" id="password" aria-describedby="help-password" placeholder="Enter Your password" required minlength="6">
                    <?php if ($validation->hasError('password')) : ?>
                        <small id="help-password" class="form-text text-danger"><?= $validation->getError('password') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-check mb-3">
                    <label class="form-check-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Keep session more than 30 minutes">
                        <input type="checkbox" class="form-check-input" name="stay_log" id="stay_log" value="yes">
                        Stay login?
                    </label>
                </div>
                <div class="form-group mb-2">
                    <button type="submit" class="btn btn-outline-primary"><i class="bi bi-box-arrow-in-right"></i> Log in</button>
                </div>
                <?= form_close() ?>
            </div>
            </div>
             <div class="d-flex justify-content-center text-center mt-4 pt-1">
                <a href="https://youtube.com/" class="text-dark"><i class="bi bi-youtube fa-lg"></i></a>
                <a href="https://www.instagram.com/" class="text-dark"><i class="bi bi-instagram fa-lg mx-4 px-2"></i></a>
                <a href="https://telegram.me/hmanhng" class="text-dark"><i class="bi bi-telegram fa-lg"></i></a>
              </div>


         
       <p class="text-left text-dark after-card">
            <small>
                Don't have an account yet?
                <a class="btn btn-outline-danger" href="<?= site_url('register') ?>" role="button">Create New</a>
                </small>
                </p>
                <p class="text-center text-dark after-card">
            <small>
                WANT TO GET YOUR MOD PANEL :-
                <a class="btn btn-outline-info" href="https://telegram.me/hmanhng" class="text-info">Order Now!</a>
            </small>
        </p>
                <p class="text-center text-dark after-card">
            <small>
                TO BUY PANEL DM HERE :-
                <a href="https://telegram.me/hmanhng" class="text-success">@hmanhng</a>
            </small>
        </p>
    </div>
</div>

<?= $this->endSection() ?>

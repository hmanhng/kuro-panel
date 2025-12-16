<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center pt-5">
    <div class="col-lg-4">
        <?= $this->include('Layout/msgStatus2') ?>

        <div class="card shadow-sm mb-5"
            style="background: linear-gradient(0.9turn, #ff0, #ebe, #00ffff); max-width: 80rem;">
            <div class="card-body h5">
                Sign Up
            </div>
            <div class="card-body">
                <?= form_open() ?>

                <div class="form-group mb-3">
                    <label for="username">Enter Username</label>
                    <input type="text" class="form-control mt-2 rounded-pill" name="username" id="username"
                        aria-describedby="help-username" placeholder="Your username" minlength="4" maxlength="24"
                        value="<?= old('username') ?>" required>
                    <?php if ($validation->hasError('username')): ?>
                        <small id="help-username"
                            class="form-text text-danger"><?= $validation->getError('username') ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="email">E-Mail</label>
                    <input type="email" class="form-control mt-2 rounded-pill" name="email" id="email"
                        aria-describedby="help-email" placeholder="Enter Your Current Mail" minlength="13"
                        maxlength="40" value="<?= old('email') ?>" required>
                    <?php if ($validation->hasError('email')): ?>
                        <small id="help-email" class="form-text text-danger"><?= $validation->getError('email') ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <input type="hidden" name="password_form" value="1">
                    <label for="fullname">Enter fullname</label>
                    <input type="text" class="form-control mt-2 rounded-pill" name="fullname" id="fullname"
                        aria-describedby="help-fullname" placeholder="Your fullname" minlength="4" maxlength="24"
                        value="<?= old('fullname') ?>" required>
                    <?php if ($validation->hasError('fullname')): ?>
                        <small id="help-fullname" class="text-danger"><?= $validation->getError('fullname') ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="password">Password</label>
                    <input type="password" class="form-control mt-2 rounded-pill" name="password" id="password"
                        aria-describedby="help-password" placeholder="Your password" minlength="6" maxlength="24"
                        required>
                    <?php if ($validation->hasError('password')): ?>
                        <small id="help-password"
                            class="form-text text-danger"><?= $validation->getError('password') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group mb-3">
                    <label for="password2">Confirm Password</label>
                    <input type="password" name="password2" id="password2" class="form-control mt-2 rounded-pill"
                        placeholder="Confirm password" aria-describedby="help-password2" minlength="6" maxlength="24"
                        required>
                    <?php if ($validation->hasError('password2')): ?>
                        <small id="help-password2"
                            class="form-text text-danger"><?= $validation->getError('password2') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group mb-3">
                    <label for="referral">Enter Referral Code</label>
                    <input type="text" name="referral" id="referral" class="form-control mt-2 rounded-pill"
                        placeholder="Referral code" aria-describedby="help-referral" value="<?= old('referral') ?>"
                        maxlength="25" required>
                    <?php if ($validation->hasError('referral')): ?>
                        <small id="help-referral"
                            class="form-text text-danger"><?= $validation->getError('referral') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group mb-3">
                    <label for="ip" class="form-label">ɪᴘ ᴀᴅᴅʀᴇss</label>
                    <input type="text" id="ip" class="form-control rounded-pill" placeholder="<?php echo $user_ip ?>"
                        readonly>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="auth-terms-condition-check">
                    <label class="form-check-label" for="auth-terms-condition-check">I accept <a
                            href="javascript: void(0);" class="text-dark">Terms and Conditions</a></label>
                </div>

                <div class="form-group mb-2 mt-3">
                    <button type="submit" class="btn btn-outline-success rounded-pill"><i
                            class="bi bi-box-arrow-in-right"></i> Sign Up</button>
                </div>
                <?= form_close() ?>

            </div>
        </div>
        <p class="text-center text-muted after-card">

            Don't have an account yet?
            <a class="btn btn-outline-success rounded-pill" href="<?= site_url('login') ?>" role="button">Login</a>

        </p>
        <p class="text-center text-muted after-card">
            <small>
                TO BUY PANEL DM HERE :-
                <a href="https://telegram.me/hmanhng" class="text-info">@hmanhng</a>
            </small>
        </p>
        </small>
        </p>
    </div>
</div>

<?= $this->endSection() ?>
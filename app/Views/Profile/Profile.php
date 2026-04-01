<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Profile Overview</h5>
                <a class="btn btn-sm btn-outline-dark" href="<?= site_url('dashboard') ?>">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 h-100">
                            <h6 class="mb-3 text-muted">Account</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Full Name</span>
                                <strong><?= esc($user->fullname) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Username</span>
                                <strong>@<?= esc($user->username) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Role</span>
                                <strong><?= esc(getLevel($user->level)) ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 h-100">
                            <h6 class="mb-3 text-muted">Uplink</h6>
                            <p class="mb-0"><?= !empty($user->uplink) ? esc($user->uplink) : '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-10">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Keys Made By You</p>
                        <h3 class="mb-0"><?= (int) $userDetails1 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total History Entries</p>
                        <h3 class="mb-0"><?= (int) $userDetails2 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Keys In Database</p>
                        <h3 class="mb-0"><?= (int) $userDetails3 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

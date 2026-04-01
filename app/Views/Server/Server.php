<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>
<style>
@media (max-width: 768px) {
    .server-toolbar {
        width: 100%;
    }
    .server-toolbar .btn {
        width: 100%;
    }
    .server-toolbar {
        flex-direction: column;
    }
}
</style>

<div class="row">
    <div class="col-lg-12">
        <?= $this->include('Layout/msgStatus') ?>
    </div>

    <div class="col-lg-12 mb-3">
        <div class="card shadow border-left-primary">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <div class="h5 mb-1 text-dark">Mod Controller</div>
                    <div class="small text-muted">Open each setting in a floating window.</div>
                </div>
                <div class="d-flex flex-wrap gap-2 server-toolbar">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modStatusModal">
                        Server Status
                    </button>
                    <?php if ((int) $user->level === 1): ?>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modFeaturesModal">
                            Features
                        </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modNameModal">
                        Mod Name
                    </button>
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modFloatingTextModal">
                        Floating Text
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modStatusModal" tabindex="-1" role="dialog" aria-labelledby="modStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modStatusModalLabel">Server Based Mod</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= form_open() ?>
                <input type="hidden" name="status_form" value="1">

                <div class="form-group mb-3">
                    <label class="d-block">Current Maintenance Mode: <span class="text-muted"><?= esc($onoff['status'] ?? '-') ?></span></label>
                    <label class="hacks d-block">
                        Maintenance Mode
                        <div class="switch">
                            <input type="checkbox" name="radios" value="on" <?php if (($onoff['status'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>
                </div>

                <div class="form-group mb-3">
                    <label for="myInput">Offline Message</label>
                    <textarea class="form-control" name="myInput" id="myInput" rows="2" placeholder="Server is under maintenance"><?= esc($onoff['myinput'] ?? '') ?></textarea>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-outline-primary">Update</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?php if ((int) $user->level === 1): ?>
    <div class="modal fade" id="modFeaturesModal" tabindex="-1" role="dialog" aria-labelledby="modFeaturesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modFeaturesModalLabel">Mod Features</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= form_open() ?>
                    <input type="hidden" name="feature_form" value="1">

                    <label class="hacks d-block">ESP
                        <div class="switch">
                            <input type="checkbox" name="ESP" value="on" <?php if (($feature['ESP'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>
                    <label class="hacks d-block">Items
                        <div class="switch">
                            <input type="checkbox" name="Item" value="on" <?php if (($feature['Item'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>
                    <label class="hacks d-block">Aim-Bot
                        <div class="switch">
                            <input type="checkbox" name="AIM" value="on" <?php if (($feature['AIM'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>
                    <label class="hacks d-block">Silent Aim
                        <div class="switch">
                            <input type="checkbox" name="SilentAim" value="on" <?php if (($feature['SilentAim'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>
                    <label class="hacks d-block">Bullet Track
                        <div class="switch">
                            <input type="checkbox" name="BulletTrack" value="on" <?php if (($feature['BulletTrack'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>
                    <label class="hacks d-block">Memory
                        <div class="switch">
                            <input type="checkbox" name="Memory" value="on" <?php if (($feature['Memory'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>
                    <label class="hacks d-block">Floating Texts
                        <div class="switch">
                            <input type="checkbox" name="Floating" value="on" <?php if (($feature['Floating'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>
                    <label class="hacks d-block">Settings
                        <div class="switch">
                            <input type="checkbox" name="Setting" value="on" <?php if (($feature['Setting'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>

                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-outline-danger">Update</button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="modNameModal" tabindex="-1" role="dialog" aria-labelledby="modNameModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modNameModalLabel">Change Mod Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= form_open() ?>
                <input type="hidden" name="modname_form" value="1">
                <div class="form-group mb-3">
                    <label for="modname">Current Mod Name: <span class="text-muted"><?= esc($row['modname'] ?? '-') ?></span></label>
                    <input type="text" name="modname" id="modname" class="form-control mt-2" placeholder="Enter your new mod name" required>
                    <?php if ($validation->hasError('modname')): ?>
                        <small class="text-danger"><?= $validation->getError('modname') ?></small>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-outline-warning">Update</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modFloatingTextModal" tabindex="-1" role="dialog" aria-labelledby="modFloatingTextModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modFloatingTextModalLabel">Change Floating Text</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="d-block">Current Mod Status: <span class="text-muted"><?= esc($ftext['_status'] ?? '-') ?></span></label>
                </div>

                <?= form_open() ?>
                <input type="hidden" name="safemode_form" value="1">
                <div class="form-group mb-3">
                    <label class="hacks d-block">
                        Safe Mode
                        <div class="switch">
                            <input type="checkbox" name="safe_mode" value="on" onchange="this.form.submit()" <?php if (($ftext['_status'] ?? '') === 'Safe'): ?>checked="checked"<?php endif; ?>>
                            <span class="slider round"></span>
                        </div>
                    </label>
                </div>
                <?= form_close() ?>

                <?= form_open() ?>
                <input type="hidden" name="_ftext_form" value="1">
                <div class="form-group mb-3">
                    <label for="_ftext">Current Floating Text: <span class="text-muted"><?= esc($ftext['_ftext'] ?? '-') ?></span></label>
                    <input type="text" name="_ftext" id="_ftext" class="form-control mt-2" placeholder="Give feedback else key removed" required>
                    <?php if ($validation->hasError('_ftext')): ?>
                        <small class="text-danger"><?= $validation->getError('_ftext') ?></small>
                    <?php endif; ?>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-outline-success">Update</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?php if (isset($user) && ((int) $user->level === 1 || (int) $user->level === 2)): ?>
<div class="modal fade" id="modHubModal" tabindex="-1" role="dialog" aria-labelledby="modHubModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-3">
                <h5 class="modal-title font-weight-bold" id="modHubModalLabel">
                    <i class="fas fa-sliders-h mr-2"></i>Mod Management Center
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-dark">
                <?= form_open('Server') ?>
                <input type="hidden" name="status_form" value="1">
                <div class="list-group list-group-flush shadow-sm">
                    <div class="list-group-item py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-primary text-white rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-server"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 font-weight-bold">Maintenance Mode</h6>
                                    <small class="text-muted">Turn ON to restrict mod access</small>
                                </div>
                            </div>
                            <label class="switch mb-0">
                                <input type="checkbox" name="radios" value="on" onchange="modHubSubmitForm(this.form)" <?php if (($onoff['status'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="mt-2">
                            <input
                                type="text"
                                name="myInput"
                                class="form-control form-control-sm bg-light border-0 px-3 py-2"
                                value="<?= esc(old('myInput') ?? '') ?>"
                                placeholder="<?= esc(($onoff['myinput'] ?? '') !== '' ? $onoff['myinput'] : 'Maintenance Message...') ?>"
                                onblur="modHubSubmitForm(this.form)">
                        </div>
                    </div>
                </div>
                <?= form_close() ?>

                <?php $isMaintenance = (($onoff['status'] ?? '') === 'on'); ?>
                <?php $isSafeMode = (($ftext['_status'] ?? '') === 'Safe'); ?>
                <?php $isFloatingEnabled = (($feature['Floating'] ?? 'off') === 'on'); ?>
                <div id="modHubControlBlock"<?= $isMaintenance ? ' style="display:none;"' : '' ?>>
                    <div class="bg-light px-3 py-2 small font-weight-bold text-uppercase text-muted border-bottom">Floating Text</div>
                    <?= form_open('Server') ?>
                    <input type="hidden" name="floating_form" value="1">
                    <div class="list-group list-group-flush pb-2">
                        <div class="list-group-item py-2 border-0 px-3">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <label class="small font-weight-bold text-muted mb-0">Enable Floating Text</label>
                                <label class="switch mb-0" style="transform: scale(0.85);">
                                    <input type="checkbox" name="Floating" value="on" onchange="modHubSubmitForm(this.form)" <?php if (($feature['Floating'] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?= form_close() ?>

                    <?= form_open('Server') ?>
                    <input type="hidden" name="_ftext_form" value="1">
                    <div class="list-group list-group-flush pb-2" id="floatingEditorWrap"<?= $isFloatingEnabled ? '' : ' style="display:none;"' ?>>
                        <div class="list-group-item py-2 border-0 px-3">
                            <label class="small font-weight-bold text-muted mb-2 d-block">Credit Text</label>
                            <input
                                type="text"
                                name="_ftext_value"
                                class="form-control form-control-sm bg-light border-0 px-3 py-2"
                                value="<?= esc(old('_ftext_value') ?? '') ?>"
                                placeholder="<?= esc(($ftext['_ftext'] ?? '') !== '' ? $ftext['_ftext'] : 'Floating text context') ?>"
                                onblur="modHubSubmitForm(this.form)">
                        </div>
                    </div>
                    <?= form_close() ?>

                    <div class="bg-light px-3 py-2 small font-weight-bold text-uppercase text-muted border-bottom border-top">Safe Mode</div>
                    <?= form_open('Server') ?>
                    <input type="hidden" name="safemode_form" value="1">
                    <div class="list-group list-group-flush pb-2">
                        <div class="list-group-item py-2 border-0 px-3">
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <label class="small font-weight-bold text-muted mb-0">Safe Mode Status</label>
                                <label class="switch mb-0" style="transform: scale(0.8);">
                                    <input type="checkbox" name="safe_mode" value="on" onchange="modHubSubmitForm(this.form)" <?php if (($ftext['_status'] ?? '') === 'Safe'): ?>checked="checked"<?php endif; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?= form_close() ?>

                    <?php if ((int) $user->level === 1): ?>
                    <div id="modFeaturesSection"<?= $isSafeMode ? '' : ' style="display:none;"' ?>>
                    <?= form_open('Server') ?>
                    <input type="hidden" name="feature_form" value="1">
                    <div class="bg-light px-3 py-2 small font-weight-bold text-uppercase text-muted border-bottom">Mod Features</div>
                    <div class="list-group list-group-flush scroll-mod-list" style="max-height: 250px; overflow-y: auto;">
                        <?php
                        $features_map = [
                            'ESP' => 'ESP',
                            'Item' => 'Items ESP',
                            'AIM' => 'Aim-Bot',
                            'SilentAim' => 'Silent Aim',
                            'BulletTrack' => 'Bullet Track',
                            'Memory' => 'Memory Hack',
                            'Setting' => 'Internal Settings',
                        ];
                        foreach ($features_map as $key => $label): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-2 border-0 px-3">
                            <span class="small font-weight-bold"><?= $label ?></span>
                            <label class="switch mb-0" style="transform: scale(0.85);">
                                <input type="checkbox" name="<?= $key ?>" value="on" onchange="modHubSubmitForm(this.form)" <?php if (($feature[$key] ?? '') === 'on'): ?>checked="checked"<?php endif; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?= form_close() ?>
                    </div>
                    <?php endif; ?>

                    <div class="bg-light px-3 py-2 small font-weight-bold text-uppercase text-muted border-bottom border-top">Identity & UI</div>
                    <div class="list-group list-group-flush pb-3">
                        <?= form_open('Server') ?>
                        <input type="hidden" name="modname_form" value="1">
                        <div class="list-group-item py-2 border-0 px-3">
                            <label class="small font-weight-bold text-muted mb-2">Mod Name</label>
                            <input
                                type="text"
                                name="modname"
                                class="form-control form-control-sm bg-light border-0 px-3 py-2"
                                value="<?= esc(old('modname') ?? '') ?>"
                                placeholder="<?= esc(($row['modname'] ?? '') !== '' ? $row['modname'] : 'Enter mod name') ?>"
                                onblur="modHubSubmitForm(this.form)">
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
                <div id="modHubRestrictedBlock"<?= $isMaintenance ? '' : ' style="display:none;"' ?>>
                    <div class="p-4 text-center bg-light">
                        <div class="text-warning mb-2"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                        <p class="small text-muted mb-0">Mod settings are restricted during maintenance.</p>
                        <p class="small text-muted font-italic">Turn off Maintenance to access all features.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($user) && ((int) $user->level === 1 || (int) $user->level === 2)): ?>
<div id="modHubToast" class="mod-hub-toast" role="status" aria-live="polite" aria-atomic="true">
    <span id="modHubToastText">Updated successfully.</span>
</div>
<style>
.mod-hub-toast{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:2000;
    max-width:min(420px, calc(100vw - 24px));
    padding:12px 16px;
    border:2px solid #1f232b;
    border-radius:12px;
    box-shadow:4px 4px 0 rgba(20, 22, 29, 0.85);
    background:#ebfff3;
    color:#116a49;
    font-weight:600;
    opacity:0;
    pointer-events:none;
    transform:translateY(10px);
    transition:opacity .18s ease, transform .18s ease;
}
.mod-hub-toast.is-visible{
    opacity:1;
    transform:translateY(0);
}
.mod-hub-toast.is-success{
    background:#d9f8e8;
    color:#0c6a45;
}
.mod-hub-toast.is-error{
    background:#ffe4df;
    color:#a22a1b;
}
@media (max-width: 576px){
    .mod-hub-toast{
        right:12px;
        bottom:12px;
    }
}

.mod-hub-anim-target{
    will-change:max-height, opacity, transform;
}
</style>
<?php endif; ?>

<?php if (session()->getFlashdata('reopen_mod_hub')): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('modHubModal');
    if (!modalEl || typeof bootstrap === 'undefined') {
        return;
    }
    var modal = new bootstrap.Modal(modalEl);
    modal.show();
});
</script>
<?php endif; ?>

<script>
function modHubSubmitForm(form) {
    if (!form) {
        return;
    }
    if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
        return;
    }
    var event = new Event('submit', { cancelable: true, bubbles: true });
    if (form.dispatchEvent(event)) {
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('modHubModal');
    if (!modalEl) {
        return;
    }

    var toastBox = document.getElementById('modHubToast');
    var toastText = document.getElementById('modHubToastText');
    var toastTimer = null;
    var sectionTimers = new WeakMap();
    var controlBlock = document.getElementById('modHubControlBlock');
    var restrictedBlock = document.getElementById('modHubRestrictedBlock');
    var floatingEditorWrap = document.getElementById('floatingEditorWrap');
    var modFeaturesSection = document.getElementById('modFeaturesSection');
    var forms = modalEl.querySelectorAll('form');

    function showStatus(message, isOk) {
        if (!toastBox || !toastText) {
            return;
        }
        toastBox.classList.remove('is-success', 'is-error');
        toastBox.classList.add(isOk ? 'is-success' : 'is-error', 'is-visible');
        toastText.textContent = message;
        if (toastTimer) {
            clearTimeout(toastTimer);
        }
        toastTimer = setTimeout(function () {
            toastBox.classList.remove('is-visible');
        }, 3200);
    }

    function refreshCsrf(name, hash) {
        if (!name || !hash) {
            return;
        }
        forms.forEach(function (f) {
            var input = f.querySelector('input[name="' + name + '"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                f.appendChild(input);
            }
            input.value = hash;
        });
    }

    function animateSection(section, shouldShow) {
        if (!section) {
            return;
        }

        var computedDisplay = window.getComputedStyle(section).display;
        var currentlyShown = computedDisplay !== 'none';
        if (currentlyShown === shouldShow) {
            return;
        }

        section.classList.add('mod-hub-anim-target');

        var oldTimer = sectionTimers.get(section);
        if (oldTimer) {
            clearTimeout(oldTimer);
        }

        section.style.overflow = 'hidden';
        section.style.transition = 'max-height .24s ease, opacity .2s ease, transform .24s ease';

        if (shouldShow) {
            section.style.display = '';
            section.style.maxHeight = '0px';
            section.style.opacity = '0';
            section.style.transform = 'translateY(-6px)';

            requestAnimationFrame(function () {
                var targetHeight = section.scrollHeight;
                section.style.maxHeight = targetHeight + 'px';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            });
        } else {
            section.style.maxHeight = section.scrollHeight + 'px';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
            section.offsetHeight;
            section.style.maxHeight = '0px';
            section.style.opacity = '0';
            section.style.transform = 'translateY(-6px)';
        }

        var timer = setTimeout(function () {
            section.style.transition = '';
            section.style.overflow = '';
            section.style.maxHeight = '';
            section.style.opacity = '';
            section.style.transform = '';
            section.style.display = shouldShow ? '' : 'none';
            sectionTimers.delete(section);
        }, 280);

        sectionTimers.set(section, timer);
    }

    forms.forEach(function (form) {
        form.setAttribute('data-ajax', '1');
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var fd = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (res) {
                return res.text().then(function (text) {
                    try {
                        return JSON.parse(text);
                    } catch (err) {
                        throw new Error('INVALID_JSON');
                    }
                });
            })
            .then(function (data) {
                var isOk = data.status === 'ok';
                showStatus(data.message || 'Updated successfully.', isOk);
                refreshCsrf(data.csrfName, data.csrfHash);
                if (!isOk) {
                    return;
                }

                if (typeof data.maintenance !== 'undefined') {
                    var isOn = data.maintenance === 'on';
                    animateSection(controlBlock, !isOn);
                    animateSection(restrictedBlock, isOn);
                }

                if (typeof data.floatingEnabled !== 'undefined') {
                    var floatingOn = !!data.floatingEnabled;
                    animateSection(floatingEditorWrap, floatingOn);
                }

                if (typeof data.safeMode !== 'undefined') {
                    var safeOn = !!data.safeMode;
                    animateSection(modFeaturesSection, safeOn);
                }
            })
            .catch(function () {
                showStatus('Update failed. Please try again.', false);
            });
        });
    });
});
</script>

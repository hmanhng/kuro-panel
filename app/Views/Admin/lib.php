<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<?= $this->include('Layout/msgStatus') ?>

<style>
    #uploadLibModal {
        background: transparent !important;
        pointer-events: none;
    }

    #uploadLibModal .modal-dialog {
        pointer-events: auto;
    }

    .retro-deploy-btn {
        border: 2px solid #1f232b !important;
        border-radius: 14px !important;
        background: linear-gradient(135deg, #ffd27b 0%, #f58634 45%, #d86729 100%) !important;
        color: #24170f !important;
        font-weight: 800 !important;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        box-shadow: 5px 5px 0 #1f232b !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
    }

    .retro-deploy-btn:hover {
        transform: translate(-1px, -1px);
        box-shadow: 6px 6px 0 #1f232b !important;
        filter: saturate(1.08);
    }

    .retro-deploy-btn:active {
        transform: translate(2px, 2px);
        box-shadow: 2px 2px 0 #1f232b !important;
    }

    .lib-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 6.2rem;
        padding: 0.18rem 0.72rem;
        border: 2px solid #1f232b;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        box-shadow: 2px 2px 0 rgba(31, 35, 43, 0.55);
    }

    .lib-status-pill--active {
        background: linear-gradient(135deg, #7de3b2 0%, #2dad79 100%);
        color: #0f3b29;
    }

    .lib-status-pill--inactive {
        background: linear-gradient(135deg, #d5d9e5 0%, #9ca3b8 100%);
        color: #2f3547;
    }

    .lib-action-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.2rem 0.6rem;
        border: 2px solid #1f232b;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none !important;
        box-shadow: 2px 2px 0 rgba(31, 35, 43, 0.55);
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }

    .lib-action-link:hover {
        transform: translate(-1px, -1px);
        box-shadow: 3px 3px 0 rgba(31, 35, 43, 0.55);
    }

    .lib-action-link--download {
        background: #dff3ff;
        color: #1968a3 !important;
    }

    .lib-action-link--activate {
        background: #f7e8d1;
        color: #8a4d08 !important;
    }

    .lib-action-link--delete {
        background: #ffe2de;
        color: #b22b1f !important;
    }

    .drive-upload-toast {
        position: fixed;
        right: max(1.25rem, env(safe-area-inset-right));
        bottom: max(1.25rem, env(safe-area-inset-bottom));
        width: min(22rem, calc(100vw - 2rem));
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.97) 0%, rgba(247, 250, 254, 0.98) 100%);
        border-radius: 0.85rem;
        box-shadow: 0 1rem 2.2rem rgba(15, 23, 42, 0.16);
        border: 1px solid rgba(148, 163, 184, 0.24);
        overflow: hidden;
        z-index: 9999;
        transform: translateY(130%);
        opacity: 0;
        pointer-events: none;
        transition: transform 0.28s ease, opacity 0.28s ease;
        backdrop-filter: blur(14px);
    }

    .drive-upload-toast.is-visible {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .drive-upload-toast__topbar {
        height: 0.2rem;
        background: rgba(226, 232, 240, 0.95);
    }

    .drive-upload-toast__progress {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #1a73e8 0%, #39a0ff 100%);
        transition: width 0.18s ease;
    }

    .drive-upload-toast__body {
        display: flex;
        gap: 0.7rem;
        align-items: flex-start;
        padding: 0.68rem 0.75rem 0.5rem;
    }

    .drive-upload-toast__icon {
        width: 2.1rem;
        height: 2.1rem;
        flex: 0 0 2.1rem;
        border-radius: 0.65rem;
        display: grid;
        place-items: center;
        color: #fff;
        background: linear-gradient(135deg, #1a73e8 0%, #0f5bd3 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
        font-size: 0.85rem;
    }

    .drive-upload-toast.is-success .drive-upload-toast__icon {
        background: linear-gradient(135deg, #188038 0%, #34a853 100%);
    }

    .drive-upload-toast.is-error .drive-upload-toast__icon {
        background: linear-gradient(135deg, #d93025 0%, #ea4335 100%);
    }

    .drive-upload-toast__content {
        min-width: 0;
        flex: 1 1 auto;
    }

    .drive-upload-toast__title {
        margin: 0;
        color: #1f2937;
        font-size: 0.88rem;
        font-weight: 700;
        line-height: 1.25;
    }

    .drive-upload-toast__meta,
    .drive-upload-toast__message {
        margin: 0.1rem 0 0;
        color: #64748b;
        font-size: 0.74rem;
        line-height: 1.3;
    }

    .drive-upload-toast__message {
        color: #475569;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .drive-upload-toast__close {
        appearance: none;
        border: 0;
        background: transparent;
        color: #94a3b8;
        width: 1.6rem;
        height: 1.6rem;
        border-radius: 999px;
        display: grid;
        place-items: center;
        transition: background-color 0.2s ease, color 0.2s ease;
        margin-top: -0.05rem;
    }

    .drive-upload-toast__close:hover {
        background: rgba(148, 163, 184, 0.12);
        color: #334155;
    }

    .drive-upload-toast__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 0.75rem 0.6rem 3.55rem;
    }

    .drive-upload-toast__percent {
        color: #1a73e8;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .drive-upload-toast.is-success .drive-upload-toast__percent {
        color: #188038;
    }

    .drive-upload-toast.is-error .drive-upload-toast__percent {
        color: #d93025;
    }

    .drive-upload-toast__hint {
        color: #94a3b8;
        font-size: 0.7rem;
    }

    @media (max-width: 576px) {
        .drive-upload-toast {
            right: 1rem;
            left: 1rem;
            bottom: max(1rem, env(safe-area-inset-bottom));
            width: auto;
        }

        .drive-upload-toast__footer {
            padding-left: 1rem;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Header Stats - All in one row -->
    <div class="row">
        <div class="col-12">
            <div class="card card-body blur shadow-blur overflow-hidden">
                <div class="row gx-4 align-items-center">
                    <div class="col-auto">
                        <div class="avatar avatar-xl position-relative">
                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow text-center border-radius-xl">
                                <i class="ni ni-folder-17 opacity-10"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1">
                                <?= $libData ? esc($libData['file']) : 'None Active' ?>
                            </h5>
                            <p class="mb-0 font-weight-bold text-sm">
                                Current Active Library
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 ms-sm-auto">
                        <div class="text-center">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Size</p>
                            <h6 class="font-weight-bolder mb-0"><?= $libData ? esc($libData['file_size']) : '0 B' ?></h6>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="text-center">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Last Update</p>
                            <h6 class="font-weight-bolder mb-0"><?= $libData ? date('d M, H:i', strtotime($libData['time'])) : 'Never' ?></h6>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-12 text-end">
                        <button type="button" class="btn btn-sm mb-0 retro-deploy-btn" data-bs-toggle="modal" data-bs-target="#uploadLibModal">
                            <i class="ni ni-cloud-upload-96 me-2"></i> Deploy New
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Width History Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0">Library Version History</h6>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Library Filename</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Size</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Payload Key</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Uploaded At</th>
                                    <th class="text-secondary opacity-7 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($libHistory)): foreach ($libHistory as $lib): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm"><?= esc($lib['file']) ?></h6>
                                                <p class="text-xs text-secondary mb-0"><?= esc(basename($lib['file_type'])) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0"><?= esc($lib['file_size']) ?></p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0 text-primary cursor-pointer" title="Click to copy" onclick="navigator.clipboard.writeText('<?= esc($lib['payload']) ?>')">
                                            <?= esc($lib['payload'] ?? '-') ?>
                                        </p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <?php if (!empty($lib['is_active'])): ?>
                                            <span class="lib-status-pill lib-status-pill--active">Active</span>
                                        <?php else: ?>
                                            <span class="lib-status-pill lib-status-pill--inactive">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold"><?= esc($lib['time']) ?></span>
                                    </td>
                                    <td class="align-middle text-center text-xs">
                                        <div class="d-flex justify-content-center gap-3">
                                            <a href="<?= base_url('lib/download/' . $lib['id']) ?>" class="lib-action-link lib-action-link--download" target="_blank">Download</a>
                                            
                                            <?php if (empty($lib['is_active'])): ?>
                                                <form action="<?= base_url('admin/lib/active') ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="set_active_lib_id" value="<?= (int) $lib['id'] ?>">
                                                    <button type="submit" class="lib-action-link lib-action-link--activate">Activate</button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if ($user->level == 1 || $user->level == 2): ?>
                                                <a href="<?= base_url('lib/delete/' . $lib['id']) ?>" 
                                                    class="lib-action-link lib-action-link--delete" 
                                                    onclick="return confirm('Delete this library permanentely from server?');">
                                                    Delete
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-sm text-secondary">No libraries uploaded yet.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="driveUploadToast" class="drive-upload-toast" aria-live="polite" aria-atomic="true">
    <div class="drive-upload-toast__topbar">
        <div id="driveUploadToastProgress" class="drive-upload-toast__progress"></div>
    </div>
    <div class="drive-upload-toast__body">
        <div class="drive-upload-toast__icon">
            <i class="ni ni-cloud-upload-96"></i>
        </div>
        <div class="drive-upload-toast__content">
            <p id="driveUploadToastTitle" class="drive-upload-toast__title">Uploading library...</p>
            <p id="driveUploadToastMeta" class="drive-upload-toast__meta">Preparing file transfer</p>
            <p id="driveUploadToastMessage" class="drive-upload-toast__message">The page stays responsive while the upload runs in the background.</p>
        </div>
        <button type="button" id="driveUploadToastClose" class="drive-upload-toast__close" aria-label="Close">
            <i class="ni ni-fat-remove"></i>
        </button>
    </div>
    <div class="drive-upload-toast__footer">
        <span id="driveUploadToastPercent" class="drive-upload-toast__percent">0%</span>
        <span id="driveUploadToastHint" class="drive-upload-toast__hint">Do not close this tab</span>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadLibModal" tabindex="-1" role="dialog" aria-labelledby="uploadLibModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder" id="uploadLibModalLabel">Deploy New Library</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <p class="text-sm">Select a <b>.so</b> library file to upload. New uploads will automatically become the active version for the API.</p>
                <form id="uploadLibForm" action="<?= base_url('lib') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="form-group mb-3">
                        <label for="libfile" class="form-control-label">SO Library File</label>
                        <input class="form-control" type="file" name="libfile" id="libfile" accept=".so" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="payload" class="form-control-label">Encryption Key (Payload)</label>
                        <input class="form-control" type="text" name="payload" id="payload" placeholder="e.g. MySecretKey123" required>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn bg-gradient-secondary btn-sm mb-0" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="uploadLibSubmit" class="btn bg-gradient-primary btn-sm mb-0">Upload & Deploy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var form = document.getElementById('uploadLibForm');
        var submitButton = document.getElementById('uploadLibSubmit');
        var modalElement = document.getElementById('uploadLibModal');
        var toast = document.getElementById('driveUploadToast');
        var progressBar = document.getElementById('driveUploadToastProgress');
        var percentText = document.getElementById('driveUploadToastPercent');
        var titleText = document.getElementById('driveUploadToastTitle');
        var metaText = document.getElementById('driveUploadToastMeta');
        var messageText = document.getElementById('driveUploadToastMessage');
        var hintText = document.getElementById('driveUploadToastHint');
        var closeButton = document.getElementById('driveUploadToastClose');
        var uploadModal = (window.bootstrap && modalElement) ? bootstrap.Modal.getOrCreateInstance(modalElement) : null;
        var autoHideTimer = null;

        if (!form || !submitButton || !toast || !progressBar || !percentText) {
            return;
        }

        if (toast.parentNode !== document.body) {
            document.body.appendChild(toast);
        }

        if (modalElement) {
            modalElement.addEventListener('shown.bs.modal', function () {
                document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                    backdrop.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
            });
        }

        function clearAutoHide() {
            if (autoHideTimer) {
                window.clearTimeout(autoHideTimer);
                autoHideTimer = null;
            }
        }

        function showToast() {
            toast.classList.add('is-visible');
        }

        function hideToast() {
            clearAutoHide();
            toast.classList.remove('is-visible');
        }

        function forceCloseUploadModal() {
            if (uploadModal) {
                uploadModal.hide();
            }

            if (modalElement) {
                modalElement.classList.remove('show');
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.style.display = 'none';
            }

            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');

            document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                backdrop.remove();
            });
        }

        function setProgress(percent) {
            var safePercent = Math.max(0, Math.min(100, percent));
            progressBar.style.width = safePercent + '%';
            percentText.textContent = safePercent + '%';
        }

        function setToastState(state) {
            toast.classList.remove('is-success', 'is-error');
            if (state === 'success') {
                toast.classList.add('is-success');
            } else if (state === 'error') {
                toast.classList.add('is-error');
            }
        }

        function updateToast(state, title, meta, message, percent, hint) {
            setToastState(state);
            titleText.textContent = title;
            metaText.textContent = meta;
            messageText.textContent = message;
            hintText.textContent = hint;
            setProgress(percent);
            showToast();
        }

        function updateCsrf(data) {
            if (!data || !data.csrfName || !data.csrfHash) {
                return;
            }

            var tokenInput = form.querySelector('input[name="' + data.csrfName + '"]');
            if (tokenInput) {
                tokenInput.value = data.csrfHash;
            }
        }

        function scheduleReload() {
            clearAutoHide();
            autoHideTimer = window.setTimeout(function () {
                window.location.reload();
            }, 1200);
        }

        closeButton.addEventListener('click', hideToast);

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearAutoHide();
            forceCloseUploadModal();

            var fileInput = document.getElementById('libfile');
            var payloadInput = document.getElementById('payload');
            if (!fileInput || !fileInput.files.length) {
                updateToast('error', 'Upload blocked', 'No library selected', 'Choose a .so file before uploading.', 0, 'Fix the input and try again');
                return;
            }

            if (!payloadInput || !payloadInput.value.trim()) {
                updateToast('error', 'Upload blocked', 'Payload key missing', 'Enter the encryption key before uploading.', 0, 'Fix the input and try again');
                return;
            }

            var xhr = new XMLHttpRequest();
            var formData = new FormData(form);
            submitButton.disabled = true;
            submitButton.textContent = 'Uploading...';

            updateToast(
                'uploading',
                'Uploading library...',
                fileInput.files[0].name,
                'Transfer started. The page remains usable during upload.',
                0,
                'Please keep this tab open'
            );

            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', function (e) {
                if (!e.lengthComputable) {
                    updateToast(
                        'uploading',
                        'Uploading library...',
                        fileInput.files[0].name,
                        'Sending file to server...',
                        15,
                        'Upload progress unavailable'
                    );
                    return;
                }

                var percent = Math.round((e.loaded / e.total) * 100);
                updateToast(
                    'uploading',
                    percent >= 100 ? 'Finalizing upload...' : 'Uploading library...',
                    fileInput.files[0].name,
                    percent >= 100 ? 'File received. Waiting for server to finish deployment.' : 'Transferring file to server...',
                    percent,
                    percent >= 100 ? 'Processing on server' : 'Transfer in progress'
                );
            });

            xhr.addEventListener('load', function () {
                submitButton.disabled = false;
                submitButton.textContent = 'Upload & Deploy';

                var data = null;
                try {
                    data = JSON.parse(xhr.responseText);
                } catch (error) {
                    data = null;
                }

                if (data) {
                    updateCsrf(data);
                }

                if (xhr.status >= 200 && xhr.status < 300 && data && data.status === 'ok') {
                    form.reset();
                    updateToast(
                        'success',
                        'Upload completed',
                        data.file || fileInput.files[0].name,
                        data.message || 'Library uploaded successfully.',
                        100,
                        'Refreshing list...'
                    );
                    scheduleReload();
                    return;
                }

                updateToast(
                    'error',
                    'Upload failed',
                    fileInput.files[0].name,
                    (data && data.message) ? data.message : 'The server could not finish the upload.',
                    100,
                    'Please try again'
                );
            });

            xhr.addEventListener('error', function () {
                submitButton.disabled = false;
                submitButton.textContent = 'Upload & Deploy';
                updateToast(
                    'error',
                    'Connection error',
                    fileInput.files[0].name,
                    'The upload was interrupted before the server responded.',
                    100,
                    'Check network and retry'
                );
            });

            xhr.addEventListener('abort', function () {
                submitButton.disabled = false;
                submitButton.textContent = 'Upload & Deploy';
                updateToast(
                    'error',
                    'Upload canceled',
                    fileInput.files[0].name,
                    'The upload was canceled before completion.',
                    0,
                    'Start the upload again'
                );
            });

            xhr.send(formData);
        });
    })();
</script>

<?= $this->endSection() ?>

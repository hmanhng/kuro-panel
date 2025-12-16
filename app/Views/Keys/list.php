<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>
        <div class="col-lg-12">
            <div class="card shadow-lg p-0 mb-5 ">
                <div class="card-body text-dark h5">
                    <div class="row">
                        <div class="col pt-1">
                            Registered Keys
                        </div>
                        <div class="col text-end">

                            <a class="btn btn-outline-danger btn-sm" onclick='return deleteAllKey()'
                                href="<?= site_url('keys/deleteKeys') ?>" input type='submit' value='deleteAllKey'><i
                                    class="bi bi-trash-fill"></i>Delete All Keys</a>

                            <a class="btn btn-outline-warning btn-sm" onclick='return resetAll()'
                                href="<?= site_url('keys/resetAll') ?>" input type='submit' value='resetAll'><i
                                    class="bi bi-bootstrap-reboot"></i> Reset All Keys</a>

                            <a class="btn btn-outline-dark btn-sm" onclick='return unusedDelete()'
                                href="<?= site_url('keys/start') ?>" input type='submit' value='unusedDelete'><i
                                    class="bi bi-trash-fill"></i> Un-Used</a>

                            <a class="btn btn-outline-dark btn-sm rounded-pill"
                                href="<?= site_url('keys/generate') ?>"><i class="bi bi-person-plus"></i> KEY</a>


                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($keylist): ?>
                        <div class="table-responsive">
                            <table id="datatable" class="table table-borderless table-hover text-center" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Game</th>
                                        <th>User Keys</th>
                                        <th>Devices</th>
                                        <th>Duration</th>
                                        <th>Expired</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Nothing keys to show</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<?= link_tag("https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css") ?>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<?= script_tag("https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js") ?>

<?= script_tag("https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js") ?>
<script>
    $(document).ready(function () {
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "desc"]
            ],
            ajax: "<?= site_url('keys/api') ?>",
            columns: [{
                data: 'id',
                name: 'id_keys'
            },
            {
                data: 'game',
            },
            {
                data: 'user_key',
                render: function (data, type, row, meta) {
                    var is_valid = (row.status == 'Active') ? "text-success" : "text-danger";
                    return `<span class="${is_valid}">${(row.user_key ? row.user_key : '&mdash;')}</span> `;
                }
            },
            {
                data: 'devices',
                render: function (data, type, row, meta) {
                    var totalDevice = (row.devices ? row.devices : 0);
                    return `<span id="devMax-${row.user_key}">${totalDevice}/${row.max_devices}</span>`;
                }
            },
            {
                data: 'duration',
                render: function (data, type, row, meta) {
                    return row.duration;
                }
            },
            {
                data: 'expired',
                name: 'expired_date',
                render: function (data, type, row, meta) {
                    return row.expired ? `<span class="badge text-dark">${row.expired}</span>` : '(not started yet)';
                }
            },
            {
                data: null,
                render: function (data, type, row, meta) {
                    var btnReset = `<button class="btn btn-outline-warning btn-sm" onclick="resetUserKey('${row.user_key}')"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="Reset key?"><i class="bi bi-bootstrap-reboot"></i></button>`;
                    var btnDelete = `<button class="btn btn-outline-danger btn-sm" onclick="deleteUserKey('${row.user_key}')"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="Delete key?"><i class="bi bi-trash"></i></button>`;
                    var btnEdits = `<a href="${window.location.origin}/keys/${row.id}" class="btn btn-outline-dark btn-sm"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="Edit key information?"><i class="bi bi-pencil-square"></i></a>`;
                    return `<div class="d-grid gap-2 d-md-block">${btnReset} ${btnDelete} ${btnEdits}</div>`;
                }
            }
            ]
        });


    });

    function deleteUserKey(keys) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'info',
                    title: 'Please wait...'
                })

                var base_url = window.location.origin;
                var api_url = `${base_url}/keys/delete`;
                $.getJSON(api_url, {
                    userkey: keys,
                    delete: 1
                },
                    function (data, textStatus, jqXHR) {
                        if (textStatus == 'success') {
                            if (data.registered) {
                                if (data.delete) {
                                    $(`#devMax-${keys}`).html(`0/${data.devices_max}`);
                                    Swal.fire(
                                        'Deleted!',
                                        'Redirecting to Key Dashboard.',
                                        'success'


                                    )
                                    location.reload()

                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        data.devices_total ? "You don't have any access to this user." : "Only Admin can delete the user.",
                                        data.devices_total ? 'error' : 'error'

                                    )
                                }
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    "User key no longer exists.",
                                    'error'
                                )
                            }
                        }
                    }
                );
            }
        });
    }

    /* function deleteUserKey(keys) {
         Swal.fire({
             title: 'Are you sure to Delete ?',
             text: "You won't be able to revert this!",
             icon: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Yes, Delete'
         }).then((result) => {
             if (result.isConfirmed) {
                 Toast.fire({
                     icon: 'info',
                     title: 'Please wait...'
                 })
 
                 var base_url = window.location.origin;
                 var api_url = `${base_url}/keys/delete`;
                 $.getJSON(api_url, {
                         userkey: keys,
                         reset: 1
                     },
                     function(data, textStatus, jqXHR) {
                         // alert('data=>'+data+'--textStatus'+textStatus+'=========jqXHR'+jqXHR);
                           Toast.fire({
                     icon: 'success',
                     title: 'key deleted.'
                 })
                           location.reload();
                         
                     }
                 );
             }
         });
     }*/

    function deleteAllKey() {
        return confirm('Are you sure you want to delete all keys?');
    };

    function resetAll() {
        return confirm('Are you sure you want to reset all keys?');
    };

    function unusedDelete() {
        return confirm('Are you sure you want to delete un-used keys?');
    };

    function resetUserKey(keys) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'info',
                    title: 'Please wait...'
                })

                var base_url = window.location.origin;
                var api_url = `${base_url}/keys/reset`;
                $.getJSON(api_url, {
                    userkey: keys,
                    reset: 1
                },
                    function (data, textStatus, jqXHR) {
                        if (textStatus == 'success') {
                            if (data.registered) {
                                if (data.reset) {
                                    $(`#devMax-${keys}`).html(`0/${data.devices_max}`);
                                    Swal.fire(
                                        'Reset!',
                                        'Your key has been reset.',
                                        'success'
                                    )
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        data.devices_total ? "You don't have any access to this user." : "User key devices already reset.",
                                        data.devices_total ? 'error' : 'warning'
                                    )
                                }
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    "User key no longer exists.",
                                    'error'
                                )
                            }
                        }
                    }
                );
            }
        });
    }

</script>

<?= $this->endSection() ?>
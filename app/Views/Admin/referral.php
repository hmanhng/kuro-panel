<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<style>
    .referral-money-group {
        display: flex;
        align-items: stretch;
        border: 2px solid #1f232b;
        border-radius: 12px;
        overflow: hidden;
        background: #fffdf9;
        box-shadow: 3px 3px 0 rgba(31, 35, 43, 0.35);
    }

    .referral-money-group:focus-within {
        box-shadow: 0 0 0 0.2rem rgba(216, 103, 41, 0.2), 3px 3px 0 rgba(31, 35, 43, 0.35);
    }

    .referral-money-symbol {
        min-width: 2.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-family: "IBM Plex Mono", monospace;
        background: #f7e9d6;
        color: #1f232b;
        border-right: 2px solid #1f232b;
    }

    .referral-money-input {
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
        min-height: 2.6rem;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <?= $this->include('Layout/msgStatus') ?>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card shadow h-10 py-2">
            <div class="card-body h5 text-dark p-3">
                Create <?= $title ?>
            </div>
            <div class="card-body">
                <?= form_open() ?>

                <div class="form-group mb-3">
                    <label for="set_saldo">You can set with multiple saldo</label>
                    <div class="input-group mt-2 referral-money-group">
                        <span class="referral-money-symbol">$</span>
                        <input type="number" class="form-control referral-money-input" name="set_saldo" id="set_saldo" minlength="1"
                            maxlength="11" value="5">
                    </div>
                    <?php if ($validation->hasError('set_saldo')): ?>
                        <small id="help-saldo" class="text-danger"><?= $validation->getError('set_saldo') ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="accExpire" class="form-label">Account Expiration</label>
                    <?= form_dropdown(['class' => 'form-select', 'name' => 'accExpire', 'id' => 'accExpire'], $accExpire, old('accExpire') ?: '') ?>
                    <?php if ($validation->hasError('accExpire')): ?>
                        <small id="help-accExpire" class="text-danger"><?= $validation->getError('accExpire') ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="accLevel" class="form-label">Account Level</label>
                    <?= form_dropdown(['class' => 'form-select', 'name' => 'accLevel', 'id' => 'accLevel'], $accLevel, old('accLevel') ?: '') ?>
                    <?php if ($validation->hasError('accLevel')): ?>
                        <small id="help-accLevel" class="text-danger"><?= $validation->getError('accLevel') ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-outline-dark">Create Code</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <?php if ($code): ?>
            <div class="card shadow h-10 py-3 mb-3">
                <div class="card-body h5 text-dark p-3">
                    Referral Created <br>Total - <?= $total_code ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless text-center text-dark" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Referral</th>
                                    <th>Hashed</th>
                                    <th>Saldo</th>
                                    <th>Level</th>
                                    <th>Expiration</th>
                                    <th>Used by</th>
                                    <th>Create by</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($code as $c): ?>
                                    <tr>
                                        <td><?= $c->id_reff ?></td>
                                        <td><?= $c->Referral ?></td>
                                        <td><?= substr($c->code, 1, 15) ?></td>
                                        <td>$<?= $c->set_saldo ?></td>
                                        <td><?= $c->level ?></td>
                                        <td><?= $c->acc_expiration ?></td>
                                        <td><?= $c->used_by ?></td>
                                        <td><?= $c->created_by ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<br><br>
<?= $this->endSection() ?>

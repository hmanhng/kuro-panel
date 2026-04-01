<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<style>
    .retro-select-panel {
        border: 2px solid #1f232b;
        border-radius: 14px;
        background: #fffaf2;
        box-shadow: 4px 4px 0 rgba(31, 35, 43, .6);
        padding: .8rem;
    }

    .retro-select-panel .form-label {
        font-weight: 700;
        margin-bottom: .45rem;
    }

    .retro-select-panel .form-select {
        background-image:
            linear-gradient(45deg, transparent 50%, #1f232b 50%),
            linear-gradient(135deg, #1f232b 50%, transparent 50%),
            linear-gradient(to right, #f7e9d6, #f7e9d6);
        background-position:
            calc(100% - 18px) calc(50% - 4px),
            calc(100% - 12px) calc(50% - 4px),
            calc(100% - 2.4rem) .3rem;
        background-size: 6px 6px, 6px 6px, 1px 1.9rem;
        background-repeat: no-repeat;
        padding-right: 2.8rem;
    }

    .retro-select-hint {
        display: block;
        margin-top: .45rem;
        font-size: .76rem;
        color: #6f665d;
        font-family: "IBM Plex Mono", monospace;
    }
</style>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <?= $this->include('Layout/msgStatus') ?>
        <?php if (session()->getFlashdata('user_key')): ?>
            <div class="alert alert-success" role="alert">
                Game : <?= session()->getFlashdata('game') ?> / <?= session()->getFlashdata('duration') ?> Days<br>
                License : <strong class="key-sensi"><?= session()->getFlashdata('user_key') ?></strong><br>
                Available for <?= session()->getFlashdata('max_devices') ?> Devices<br>
                <small>
                    <i>Duration will start when license login.</i><br>
                    <i class="bi bi-wallet"></i> Saldo Reduce :
                    <span class="text-danger">-<?= session()->getFlashdata('fees') ?></span>
                    (Total left <?= $user->saldo ?>$)
                </small>
            </div>
        <?php endif; ?>
        <div class="card shadow h-100 py-2">
            <div class="card-header text-center font-weight-bold text-dark">
                <div class="row">
                    <div class="col pt-1">
                        Create Key
                    </div>
                    <div class="col text-end">
                        <a class="btn btn-sm btn-outline-dark rounded-circle" href="<?= site_url('keys') ?>"><i
                                class="bi bi-people"></i></a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?= form_open() ?>

                <div class="row">
                    <div class="form-group col-lg-6 mb-3">
                        <div class="retro-select-panel">
                        <label for="game" class="form-label">Games</label>
                        <?= form_dropdown(['class' => 'form-select', 'name' => 'game', 'id' => 'game'], $game, old('game') ?: '') ?>
                        <small id="gameHint" class="retro-select-hint">Choose target game</small>
                        <?php if ($validation->hasError('game')): ?>
                            <small id="help-game" class="text-danger"><?= $validation->getError('game') ?></small>
                        <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group col-lg-6 mb-3 ">
                        <label for="max_devices" class="form-label">Max Devices</label>
                        <input type="number" name="max_devices" id="max_devices" class="form-control"
                            placeholder="1" value="<?= old('max_devices') ?: 1 ?>">
                        <?php if ($validation->hasError('game')): ?>
                            <small id="help-max_devices"
                                class="text-danger"><?= $validation->getError('max_devices') ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="retro-select-panel">
                    <label for="duration" class="form-label">Duration</label>
                    <?= form_dropdown(['class' => 'form-select', 'name' => 'duration', 'id' => 'duration'], $duration, old('duration') ?: '') ?>
                    <small id="durationHint" class="retro-select-hint">Select key lifetime</small>
                    <?php if ($validation->hasError('duration')): ?>
                        <small id="help-duration" class="text-danger"><?= $validation->getError('duration') ?></small>
                    <?php endif; ?>
                    </div>
                </div>
                <br>
                <label class="form-check-label" for="check">Custom Key</label>
                <input class="form-check-input" type="checkbox" value="" name="check" onchange="fupi(this)"
                    id="check">
                <br>


                <br>
                <label for="custom" id="cuslabel" class="form-label">Type Your Key</label>
                <input type="text" minlength="4" maxlength="32" name="cuslicense" class="form-control"
                    id="custom"></input>

                <label for="hulala" id="labula" class="form-label">Bulk Keys</label>
                <select class="form-select" aria-label="Default select example" id="hulala"
                    name="loopcount">

                    <?php if (!empty($loopcount) && is_array($loopcount)): ?>
                        <?php foreach ($loopcount as $value => $label): ?>
                            <option value="<?= $value ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback if loopcount variable is missing or empty -->
                        <option value="1">1 Key</option>
                        <option value="5">5 Keys</option>
                        <option value="10">10 Keys</option>
                        <option value="25">25 Keys</option>
                        <option value="50">50 Keys</option>
                    <?php endif; ?>

                </select>

                <br>

                <input type="text" id="textinput" name="custominput" hidden>

                <div class="form-group mb-3">
                    <label for="estimation" class="form-label">Estimation</label>
                    <input type="text" id="estimation" class="form-control"
                        placeholder="Your order will total" readonly>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-outline-dark">Create</button>
                </div>
                <?= form_close() ?>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        var price = JSON.parse('<?= $price ?>');
        getPrice(price);
        refreshSelectHint();
        // When selected
        $("#max_devices, #duration, #game").change(function () {
            getPrice(price);
            refreshSelectHint();
        });
        // try to get price
        function getPrice(price) {
            var price = price;
            var device = $("#max_devices").val();
            var durate = $("#duration").val();
            var gprice = price[durate];
            if (gprice != NaN) {
                var result = (device * gprice);
                $("#estimation").val(result);
            } else {
                $("#estimation").val('Estimation error');
            }
        }

        function refreshSelectHint() {
            var gameText = $("#game option:selected").text() || "Choose target game";
            var durationText = $("#duration option:selected").text() || "Select key lifetime";
            $("#gameHint").text("Selected: " + gameText);
            $("#durationHint").text("Selected: " + durationText);
        }
    });

    function getOption() {
        var kop = document.getElementById('keysmode').value;


    }
    $(document).ready(function () {
        document.getElementById("custom").style.display = "none";
        document.getElementById("cuslabel").style.display = "none";
    });


    function fupi(obj) {
        if ($(obj).is(":checked")) {
            //recommended W3C HTML5 syntax for boolean attributes


            document.getElementById("custom").style.display = "block";
            document.getElementById("cuslabel").style.display = "block";
            $('#hulala option').prop('selected', function () {
                return this.defaultSelected;
            });
            document.getElementById("hulala").style.display = "none";
            document.getElementById("labula").style.display = "none";
            document.getElementById("textinput").value = "custom";
            const input = document.getElementById('custom');
            input.removeAttribute('required');
        } else {
            document.getElementById("custom").style.display = "none";
            document.getElementById("cuslabel").style.display = "none";
            document.getElementById("hulala").style.display = "block";
            document.getElementById("labula").style.display = "block";
            document.getElementById("textinput").value = "auto";
            const input = document.getElementById('custom');

            // ✅ Set required attribute
            //input.setAttribute('required', '');

            // ✅ Remove required attribute
            // 
        }

    }
</script>
<?= $this->endSection() ?>

<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-12">
        <?= $this->include('Layout/msgStatus') ?>
    </div>
</div>
<!----><!----><!----><!----><!----><!----><!----><!----><!----><!---->
<?php if ($user->level != 2): ?>
    <div class="col-lg-6">
        <div class="card card bg-gradient-light shadow h-100 py-2 mb-3">
            <div class="card-body h5 p-3 text-dark">
                𝑺𝒆𝒓𝒗𝒆𝒓 𝑩𝒂𝒔𝒆𝒅 𝑴𝒐𝒅</div>
            <div class="card-body">
                <?= form_open() ?>
                <input type="hidden" name="status_form" value="1">
                <div class="form-group mb-3">
                    <label for="status">Current Maintenance Mode : <font size="2" color="#a39c9b">
                            <?php echo $onoff['status']; ?>
                        </font></label>
                    <div class="input-group mb-3">
                        <label id="esp" class="hacks">
                            𝐌𝐚𝐢𝐧𝐭𝐞𝐧𝐚𝐧𝐜𝐞 𝐌𝐨𝐝𝐞
                            <div class="switch">
                                <input type="checkbox" name="radios" id="radio" value="on" <?php if ($onoff['status'] == "on") { ?> checked="checked" <?php } ?>>
                                <span class="slider round" />
                            </div>
                        </label>
                    </div>
                    <label for="modname">𝑶𝒇𝒇𝒍𝒊𝒏𝒆 𝑴𝒔𝒈 : <font size="2" color="#a39c9b">
                            <?php echo $onoff['myinput']; ?>
                        </font></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">Offline Msg</span>
                        </div>
                        <textarea class="form-control" placeholder="𝑆𝑒𝑟𝑣𝑒𝑟 𝑖𝑠 𝑈𝑛𝑑𝑒𝑟 𝑀𝑎𝑖𝑛𝑡𝑎𝑖𝑛𝑎𝑛𝑐𝑒"
                            name="myInput" id="myInput" id="exampleFormControlTextarea1" rows="1"></textarea>
                    </div>
                    <?php if ($validation->hasError('modname')): ?>
                        <small id="help-modname" class="text-danger"><?= $validation->getError('modname') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group my-2">
                    <button type="submit" class="btn btn-outline-primary rounded-pill">𝑼𝒑𝒅𝒂𝒕𝒆</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<!----><!----><!----><!----><!----><!----><!----><!---->
<div class="col-lg-6">
    <div class="card card bg-gradient-light shadow h-100 py-2 mb-3">
        <div class="card-body h5 p-3 text-dark">
            𝐌𝐨𝐝 𝐅𝐞𝐚𝐭𝐮𝐫𝐞
        </div>
        <div class="card-body">
            <?= form_open() ?>

            <input type="hidden" name="feature_form" value="1">
            <div class="form-group mb-3">
                <label for="status">Current Status : ESP - <font color="#a39c9b"><?php echo $feature['ESP']; ?>
                    </font> Items - <font color="#a39c9b"><?php echo $feature['Item']; ?></font> AIM - <font
                        color="#a39c9b"><?php echo $feature['AIM']; ?></font> SilentAim - <font color="#a39c9b">
                        <?php echo $feature['SilentAim']; ?></font> BulletTrack - <font color="#a39c9b">
                        <?php echo $feature['BulletTrack']; ?></font> Memory - <font color="#a39c9b">
                        <?php echo $feature['Memory']; ?></font> Floating Texts - <font color="#a39c9b">
                        <?php echo $feature['Floating']; ?></font> Setting - <font color="#a39c9b">
                        <?php echo $feature['Setting']; ?></font></label>
                <label id="ESP" class="hacks">
                    𝐄𝐒𝐏
                    <div class="switch">
                        <input type="checkbox" name="ESP" id="ESP" value="on" <?php if ($feature['ESP'] == "on") { ?>
                                checked="checked" <?php } ?>>
                        <span class="slider round" />
                    </div>
                </label>
                <label id="Item" class="hacks">
                    Items
                    <div class="switch">
                        <input type="checkbox" name="Item" id="Item" value="on" <?php if ($feature['Item'] == "on") { ?>
                                checked="checked" <?php } ?>>
                        <span class="slider round" />
                    </div>
                </label>
                <label id="AIM" class="hacks">
                    𝐀𝐢𝐦-𝐁𝐨𝐭
                    <div class="switch">
                        <input type="checkbox" name="AIM" id="AIM" value="on" <?php if ($feature['AIM'] == "on") { ?>
                                checked="checked" <?php } ?>>
                        <span class="slider round" />
                    </div>
                </label>
                <label id="SilentAim" class="hacks">
                    Silent Aim
                    <div class="switch">
                        <input type="checkbox" name="SilentAim" id="SilentAim" value="on" <?php if ($feature['SilentAim'] == "on") { ?> checked="checked" <?php } ?>>
                        <span class="slider round" />
                    </div>
                </label>
                <label id="BulletTrack" class="hacks">
                    𝐁𝐮𝐥𝐥𝐞𝐭 𝐓𝐫𝐚𝐜𝐤
                    <div class="switch">
                        <input type="checkbox" name="BulletTrack" id="BulletTrack" value="on" <?php if ($feature['BulletTrack'] == "on") { ?> checked="checked" <?php } ?>>
                        <span class="slider round" />
                    </div>
                </label>
                <label id="Memory" class="hacks">
                    Memory
                    <div class="switch">
                        <input type="checkbox" name="Memory" id="Memory" value="on" <?php if ($feature['Memory'] == "on") { ?> checked="checked" <?php } ?>>
                        <span class="slider round" />
                    </div>
                </label>
                <label id="Floating" class="hacks">
                    Floating Texts
                    <div class="switch">
                        <input type="checkbox" name="Floating" id="Floating" value="on" <?php if ($feature['Floating'] == "on") { ?> checked="checked" <?php } ?>>
                        <span class="slider round" />
                    </div>
                </label>
                <label id="Setting" class="hacks">
                    Settings
                    <div class="switch">
                        <input type="checkbox" name="Setting" id="Setting" value="on" <?php if ($feature['Setting'] == "on") { ?> checked="checked" <?php } ?>>
                        <span class="slider round" />
                    </div>
                </label>
                <div class="form-group my-2">
                    <button type="submit" class="btn btn-outline-danger rounded-pill">
                        𝑼𝒑𝒅𝒂𝒕𝒆
                    </button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    <!----><!----><!----><!----><!----><!----><!----><!----><!----><!---->
    <div class="col-lg-12">
        <div class="card card bg-gradient-light shadow h-100 py-2 mb-3">
            <div class="card-body h5 p-3 text-dark">
                𝑪𝒉𝒂𝒏𝒈𝒆 𝑴𝒐𝒅 𝑵𝒂𝒎𝒆</div>
            <div class="card-body">
                <?= form_open() ?>
                <input type="hidden" name="modname_form" value="1">
                <div class="form-group mb-3">
                    <label for="modname">Current Mod Name: <font size="2" color="#a39c9b"><?php echo $row['modname']; ?>
                        </font></label>
                    <input type="text" name="modname" id="modname" class="form-control mt-2"
                        placeholder="𝐸𝑛𝑡𝑒𝑟 𝑌𝑜𝑢𝑟 𝑁𝑒𝑤 𝑀𝑜𝑑 𝑁𝑎𝑚𝑒" aria-describedby="help-modname"
                        REQUIRED>
                    <?php if ($validation->hasError('modname')): ?>
                        <small id="help-modname" class="text-danger"><?= $validation->getError('modname') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group my-2">
                    <button type="submit" class="btn btn-outline-warning rounded-pill">𝑼𝒑𝒅𝒂𝒕𝒆</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    <!----><!----><!----><!----><!----><!----><!----><!----><!----><!---->
    <div class="col-lg-12">
        <div class="card card bg-gradient-light shadow h-100 py-2 mb-3">
            <div class="card-body h5 p-3 text-dark">
                𝑪𝒉𝒂𝒏𝒈𝒆 𝑭𝒍𝒐𝒂𝒕𝒊𝒏𝒈 𝑻𝒆𝒙𝒕</div>
            <div class="card-body">
                <?= form_open() ?>
                <input type="hidden" name="_ftext" value="1">

                <label for="status">
                    Current Mod Status:
                    <font size="2" color="#a39c9b">
                        <?php echo $ftext['_status']; ?>
                    </font>
                </label>
                <div class="input-group mb-3">
                    <label id="esp" class="hacks">
                        𝐒𝐚𝐟𝐞 𝐌𝐨𝐝𝐞
                        <div class="switch">
                            <input type="checkbox" name="_ftextr" id="_ftextr" value="Safe" <?php if ($ftext['_status'] == "Safe") { ?> checked="checked" <?php } ?>>
                            <span class="slider round" />
                        </div>
                    </label>
                </div>
                <div class="form-group mb-3">
                    <label for="_ftext">Current Floating Text: <font size="2" color="#a39c9b">
                            <?php echo $ftext['_ftext']; ?>
                        </font></label>
                    <input type="text" name="_ftext" id="_ftext" class="form-control mt-2"
                        placeholder="𝐺𝑖𝑣𝑒 𝐹𝑒𝑒𝑑𝑏𝑎𝑐𝑘 𝐸𝑙𝑠𝑒 𝐾𝑒𝑦 𝑅𝑒𝑚𝑜𝑣𝑒𝑑!"
                        aria-describedby="help-_ftext" REQUIRED>
                    <?php if ($validation->hasError('_ftext')): ?>
                        <small id="help-_ftext" class="text-danger"><?= $validation->getError('_ftext') ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group my-2">
                    <button type="submit" class="btn btn-outline-success rounded-pill">𝑼𝒑𝒅𝒂𝒕𝒆</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    <!----><!----><!----><!----><!----><!----><!----><!----><!----><!---->
    </br>
</div>
<?= $this->endSection() ?>
<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Institute Details','aes'); ?></h2>

    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back','aes'); ?></a>
    </div>
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;"><b><?= __('Institution Information','aes'); ?></b></h3>
                        <table class="form-table table-customize" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Name','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= ucwords($institute->name); ?>" readonly>
                                        <input type="hidden" name="institute_id" id="institute_id" value="<?= $institute->id; ?>">
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Phone','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $institute->phone; ?>" readonly>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('Email','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $institute->email; ?>" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Country','aes'); ?></b></label><br>
                                        <input type="text" name="country" style="width:100%;" value="<?= get_name_country($institute->country); ?>" readonly>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('State','aes'); ?></b></label><br>
                                        <input type="text" value="<?= ucwords($institute->state); ?>" style="width:100%;" readonly> 
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('City','aes'); ?></b></label><br>
                                        <input type="text" value="<?= ucwords($institute->city); ?>" style="width:100%;" readonly> 
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                        <table class="form-table" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Address','aes'); ?></b></label><br>
                                        <input type="text" name="text" value="<?= $institute->address; ?>" style="width:100%;" readonly>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                        <table class="form-table" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Level','aes'); ?></b></label><br>
                                        <input type="text" name="text" value="<?= get_name_level($institute->level_id); ?>" style="width:100%" readonly>
                                    </th>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;"><b><?= __('Contact Information','aes'); ?></b></h3>
                        <table class="form-table table-customize" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Rector Name','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= ucwords($institute->name_rector); ?>" readonly>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Rector Lastname','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= ucwords($institute->lastname_rector); ?>" readonly>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('Phone','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= ucwords($institute->phone_rector); ?>" readonly>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <h3 style="margin-bottom:0px;text-align:center;"><b><?= __('Reference','aes'); ?></b></h3>
                        <table class="form-table" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Reference','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%;" name="reference" value="<?= get_name_reference($institute->reference); ?>" readonly>
                                    </th>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <?php if($institute->status == 0): ?>
                            <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                <button data-id="1" data-title="<?= __('Approve Institution','aes'); ?>" data-message="<?= __('Do you want to approve this institution?','aes'); ?>" class="button button-primary change-status-institute"><?= __('Approved','aes'); ?></button>
                                <button data-id="2" data-title="<?= __('Decline Institution','aes'); ?>" data-message="<?= __('Do pou you want to decline this institution?','aes'); ?>" class="button button-danger change-status-institute"><?= __('Declined','aes'); ?></button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    include(plugin_dir_path(__FILE__).'modal-status-institute.php');
?>

<div class="wrap">
    <?php if(isset($institute) && !empty($institute)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Institute Details','aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Institute','aes'); ?></h2>
    <?php endif; ?>

    <?php if(isset($_COOKIE['message']) && !empty($_COOKIE['message'])){ ?>
        <div class="notice notice-success is-dismissible"><p><?= $_COOKIE['message']; ?></p></div>
        <?php setcookie('message','',time(),'/'); ?>
    <?php } ?>
    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=list_admin_institutes_partner_registered_content'); ?>"><?= __('Back','aes'); ?></a>
    </div>
    <?php if(isset($institute) && !empty($institute)): ?>
        <?php if($institute->status == 1): ?>
        <div style="display:flex;width:100%;justify-content:end;">
            <button data-id="<?= $_GET['institute_id']; ?>" id="button-delete-institute-alliance" class="button button-danger"><span class="dashicons dashicons-trash"></span><?= __('Delete','aes'); ?></button>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post" action="<?= admin_url('admin.php?page=list_admin_institutes_partner_registered_content&action=save_institute_details'); ?>">
                            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;"><b><?= __('Institution Information','aes'); ?></b></h3>
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if(isset($institute) && !empty($institute)): ?>
                                                <label for="input_id"><b><?= __('Name','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="name" style="width:100%" value="<?= ucwords($institute->name); ?>" <?= ($institute->status == 0) ? 'readonly' : 'required' ?>> 
                                                <input type="hidden" name="institute_id" id="institute_id" value="<?= $institute->id; ?>">
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Name','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="name" style="width:100%" value="" required> 
                                                <input type="hidden" name="institute_id" id="institute_id" value="">
                                            <?php endif; ?>
                                        </th>
                                        <td>
                                            <?php if(isset($institute) && !empty($institute)): ?>
                                                <label for="input_id"><b><?= __('Phone','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" id="phone" name="phone" style="width:100%" value="<?= $institute->phone; ?>" <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                                <input type="hidden" name="phone_hidden" id="phone_hidden" style="width:100%" value="<?= $institute->phone; ?>">
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Phone','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" id="phone" name="phone" style="width:100%" value="" required>
                                                <input type="hidden" name="phone_hidden" id="phone_hidden" style="width:100%" value="">
                                            <?php endif; ?>
                                        </td>
                                        <td> 
                                            <?php if(isset($institute) && !empty($institute)): ?>
                                                <label for="input_id"><b><?= __('Email','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="email" name="email" style="width:100%" value="<?= $institute->email; ?>" <?= ($institute->status == 0) ? 'readonly' : 'required'; ?> readonly>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Email','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="email" name="email" style="width:100%" value="">
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">

                                            <?php if(isset($institute) && !empty($institute)): ?>

                                                <label for="input_id"><b><?= __('Country','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <?php if($institute->status == 0): ?>
                                                    <input type="text" name="country" style="width:100%;" value="<?= get_name_country($institute->country); ?>" readonly>
                                                <?php elseif($institute->status == 1): ?>

                                                    <select name="country" required>
                                                        <?php foreach($countries as $key => $country){ ?>
                                                            <option value="<?= $key; ?>" <?= ($institute->country == $key) ? 'selected' : ''; ?>><?= $country ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php endif; ?>

                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Country','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <select name="country" required>
                                                    <?php foreach($countries as $key => $country){ ?>
                                                        <option value="<?= $key; ?>"><?= $country ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php endif; ?>

                                        </th>
                                        <td>
                                            <?php if(isset($institute) && !empty($institute)): ?>
                                                <label for="input_id"><b><?= __('State','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="state" value="<?= ucwords($institute->state); ?>" style="width:100%;" <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>> 
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('State','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="state" value="" style="width:100%;" required> 
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($institute) && !empty($institute)): ?>
                                                <label for="input_id"><b><?= __('City','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="city" value="<?= ucwords($institute->city); ?>" style="width:100%;" <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>> 
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('City','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="city" value="" style="width:100%;" required> 
                                            <?php endif; ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="form-table" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if(isset($institute) && !empty($institute)): ?>
                                                <label for="input_id"><b><?= __('Address','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <textarea name="address" row="8" style="resize:none;width:100%;" <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>><?= $institute->address; ?></textarea>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Address','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <textarea name="address" row="8" style="resize:none;width:100%;" required></textarea>
                                            <?php endif; ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="form-table" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if(isset($institute) && !empty($institute)): ?>

                                                <label for="input_id"><b><?= __('Level','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <?php if($institute->status == 0): ?>
                                                    <input type="text" name="text" value="<?= get_name_level($institute->level_id); ?>" style="width:100%" readonly>
                                                <?php elseif($institute->status == 1): ?>
                                                    <select name="level" required style="width:100%;">
                                                        <option value="1" <?= ($institute->level_id == 1) ? 'selected' : ''; ?>><?= __('Primary','aes'); ?></option>
                                                        <option value="2" <?= ($institute->level_id == 2) ? 'selected' : ''; ?>><?= __('High School','aes'); ?></option>
                                                    </select>
                                                <?php endif; ?>


                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Level','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <select name="level" required style="width:100%;">
                                                    <option value="1"><?= __('Primary','aes'); ?></option>
                                                    <option value="2"><?= __('High School','aes'); ?></option>
                                                </select>
                                            <?php endif; ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;"><b><?= __('Contact Information','aes'); ?></b></h3>
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if(isset($institute) && !empty($institute)): ?>
                                                <label for="input_id"><b><?= __('Rector Name','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="rector_name" style="width:100%" value="<?= ucwords($institute->name_rector); ?>" <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Rector Name','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="rector_name" style="width:100%" value="" required>
                                            <?php endif; ?>
                                        </th>
                                        <td>
                                            <?php if(isset($institute) && !empty($institute)): ?>
                                                <label for="input_id"><b><?= __('Rector Lastname','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="rector_last_name" style="width:100%" value="<?= ucwords($institute->lastname_rector); ?>" <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Rector Lastname','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="rector_last_name" style="width:100%" value="" required>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($institute) && !empty($institute)): ?>
                                                <label for="input_id"><b><?= __('Phone','aes'); ?></b><?= ($institute->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" id="rector_phone" name="rector_phone" style="width:100%" value="<?= ucwords($institute->phone_rector); ?>" <?= ($institute->status == 0) ? 'readonly' : 'required'; ?>>
                                                <input type="hidden" id="rector_phone_hidden" name="rector_phone_hidden" style="width:100%" value="<?= ucwords($institute->phone_rector); ?>">
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Phone','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" id="rector_phone" name="rector_phone" style="width:100%" value="" required>
                                                <input type="hidden" id="rector_phone_hidden" name="rector_phone_hidden" style="width:100%" value="">
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php if(isset($institute) && !empty($institute)): ?>
                                <?php if($institute->status == 0): ?>
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
                                <?php endif; ?>
                            <?php else: ?>
                                <h3 style="margin-bottom:0px;text-align:center;"><b><?= __('Reference','aes'); ?></b></h3>
                                <table class="form-table" style="margin-top:0px;">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="font-weight:400;">
                                                <label for="input_id"><b><?= __('Reference','aes'); ?></b><span class="text-danger">*</span></label><br>
                                                <select name="reference">
                                                    <option value="3"><?= __('Email','aes'); ?></option>
                                                    <option value="4"><?= __('Internet search','aes'); ?></option>
                                                    <option value="5"><?= __('On-site event','aes'); ?></option>
                                                </select>
                                            </th>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php endif; ?>

                            <?php if(isset($institute) && !empty($institute)): ?>
                                <?php if($institute->status == 0): ?>
                                    <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                        <button type="button" data-id="1" data-title="<?= __('Approve Institution','aes'); ?>" data-message="<?= __('Do you want to approve this institution?','aes'); ?>" class="button button-primary change-status-institute"><?= __('Approve','aes'); ?></button>
                                        <button type="button" data-id="2" data-title="<?= __('Decline Institution','aes'); ?>" data-message="<?= __('Do pou you want to decline this institution?','aes'); ?>" class="button button-danger change-status-institute"><?= __('Declined','aes'); ?></button>
                                    </div>
                                <?php elseif($institute->status == 1): ?>
                                    <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                        <button type="submit" class="button button-primary"><?= __('Saves changes','aes'); ?></button>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                    <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                        <button type="submit" class="button button-primary"><?= __('Add Institute','aes'); ?></button>
                                    </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    include(plugin_dir_path(__FILE__).'modal-delete-institute-alliance.php');
?>
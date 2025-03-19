<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Alliance Details','edusystem'); ?></h2>

    <?php if(isset($_COOKIE['message']) && !empty($_COOKIE['message'])){ ?>
        <div class="notice notice-success is-dismissible"><p><?= $_COOKIE['message']; ?></p></div>
        <?php setcookie('message','',time(),'/'); ?>
    <?php } ?>
    <?php if(isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])){ ?>
        <div class="notice notice-error is-dismissible"><p><?= $_COOKIE['message-error']; ?></p></div>
        <?php setcookie('message-error','',time(),'/'); ?>
    <?php } ?>
    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=add_admin_partners_content&section_tab=all_alliances'); ?>"><?= __('Back','edusystem'); ?></a>
    </div>
    <?php if(isset($alliance) && !empty($alliance)): ?>
        <?php if($alliance->status == 1): ?>
        <div style="display:flex;width:100%;justify-content:end;">
            <button data-id="<?= $_GET['alliance_id']; ?>" id="button-delete-alliance" class="button button-danger"><span class="dashicons dashicons-trash"></span><?= __('Delete','edusystem'); ?></button>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <form method="post" action="<?= __('admin.php?page=add_admin_partners_content&action=save_setting_alliance'); ?>">
                            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;"><b><?= __('General Information','edusystem'); ?></b></h3>
                            <table class="form-table table-customize" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <?php if(isset($alliance) && !empty($alliance)): ?>
                                            <th scope="row" style="font-weight:400;">
                                                <label for="input_id"><b><?= __('Franchise code','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="required">*</span>' : ''; ?></label><br>
                                                <input type="text" style="width:100%" id="code" name="code" value="<?= (!empty($alliance->code)) ? $alliance->code : ''; ?>" <?= ($alliance->status == 1) ? 'required' : 'readonly'; ?>>
                                                <input type="hidden" id="alliance_id" name="alliance_id" value="<?= $alliance->id; ?>" required>
                                            </th>
                                        <?php else: ?>
                                            <th scope="row" style="font-weight:400;">
                                                <label for="input_id"><b><?= __('Franchise code','edusystem'); ?></b><span class="required">*</span></label><br>
                                                <input type="text" style="width:100%" id="code" name="code" value="" required>
                                                <input type="hidden" id="alliance_id" name="alliance_id" value="" required>
                                            </th>
                                        <?php endif; ?>

                                        <?php if(isset($alliance) && !empty($alliance)): ?>
                                            <?php if($alliance->status == 1): ?>
                                                <td>
                                                    <label for="input_id"><b><?= __('Franchise Type','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="required">*</span>' : ''; ?></label><br>
                                                    <select style="width:100%" name="type" id="type" value="" required>
                                                        <option value=""></option>
                                                        <option value="1" <?= ($alliance->type == '1') ? 'selected' : ''; ?>><?= __('Junior','edusystem'); ?></option>
                                                        <option value="2" <?= ($alliance->type == '2') ? 'selected' : ''; ?>><?= __('Senior','edusystem'); ?></option>
                                                    </select>
                                                </td>
                                            <?php else: ?>
                                                <td>
                                                    <label for="input_id"><b><?= __('Franchise Type','edusystem'); ?></b></label><br>
                                                    <input type="text" name="type" id="type" style="width:100%" value="<?= (!empty($alliance->type)) ? get_name_type($alliance->type) : __('Type not assigned','edusystem'); ?>" readonly>
                                                </td>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <td>
                                                <label for="input_id"><b><?= __('Franchise Type','edusystem'); ?></b><span class="required">*</span></label><br>
                                                <select style="width:100%" name="type" id="type"required>
                                                    <option value=""></option>
                                                    <option value="1"><?= __('Junior','edusystem'); ?></option>
                                                    <option value="2"><?= __('Senior','edusystem'); ?></option>
                                                </select>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>

                                        <th scope="row" style="font-weight:400;">
                                            <?php if(isset($alliance) && !empty($alliance)): ?>
                                                    <label for="input_id"><b><?= __('Name','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                    <input name="name" type="text" style="width:100%" value="<?= ucwords($alliance->name); ?>" <?= ($alliance->status == 1) ? 'required' : 'readonly'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Name','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <input name="name" type="text" style="width:100%" value="" required>
                                            <?php endif; ?>
                                        </th>

                                        <td>
                                            <?php if(isset($alliance) && !empty($alliance)): ?>
                                                <label for="input_id"><b><?= __('Last name','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="last_name" style="width:100%" value="<?= ucwords($alliance->last_name); ?>" <?= ($alliance->status == 1) ? 'required' : 'readonly'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Last name','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="last_name" style="width:100%" value="" required>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($alliance) && !empty($alliance)): ?>
                                                <label for="input_id"><b><?= __('Legal name','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="text-danger">*</span>' : '';  ?></label><br>
                                                <input type="text" name="legal_name" style="width:100%" value="<?= ucwords($alliance->name_legal); ?>" <?= ($alliance->status ==1 ) ? 'required' : 'readonly'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Legal name','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="legal_name" style="width:100%" value="" required>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if(isset($alliance) && !empty($alliance)): ?>
                                                <label for="input_id"><b><?= __('Phone','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" id="number_phone" name="number_phone" style="width:100%" value="<?= $alliance->phone; ?>" <?= ($alliance->status == 1) ? 'required' : 'readonly'; ?>>
                                                <input type="hidden" style="width:100%" name="phone_hidden" id="phone_hidden" value="<?= $alliance->phone; ?>" >
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Phone','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="number_phone" id="number_phone" style="width:100%" value="" required>
                                                <input type="hidden" style="width:100%" name="phone_hidden" id="phone_hidden" value="">
                                            <?php endif; ?>
                                        </th>
                                        <td>
                                            <?php if(isset($alliance) && !empty($alliance)): ?>
                                                <label for="input_id"><b><?= __('Email','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="email" name="email" style="width:100%" value="<?= $alliance->email; ?>" readonly>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Email','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="email" name="email" style="width:100%" value="" required>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($alliance) && !empty($alliance)): ?>
                                                <?php if($alliance->status == 1): ?>
                                                    <label for="input_id"><b><?= __('Country','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                    <select name="country" style="min-width:100%">
                                                        <?php foreach($countries as $key => $country):  ?>
                                                            <option value="<?= $key; ?>" <?= ($key == $alliance->country)  ? 'selected' : ''; ?>><?= $country; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <label for="input_id"><b><?= __('Country','edusystem'); ?></b></label><br>
                                                    <input type="text" style="width:100%" value="<?= get_name_country($alliance->country); ?>" readonly>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Country','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <select name="country" style="min-width:100%">
                                                    <?php foreach($countries as $key => $country): ?>
                                                        <option value="<?= $key; ?>"><?= $country ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <?php if(isset($alliance) && !empty($alliance)): ?>
                                                <label for="input_id"><b><?= __('State','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="state" style="width:100%" value="<?= $alliance->state ?>" <?= ($alliance->status == 1) ? 'required' : 'readonly'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('State','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="state" style="width:100%" value="" required>
                                            <?php endif; ?>
                                        </th>
                                        <td>
                                            <?php if(isset($alliance) && !empty($alliance)): ?>
                                                <label for="input_id"><b><?= __('City','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="city" style="width:100%"  value="<?= $alliance->city ?>"<?= ($alliance->status == 1) ? 'required' : 'readonly'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('City','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="city" style="width:100%"  value="" required>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($alliance) && !empty($alliance)): ?>
                                                <label for="input_id"><b><?= __('Fee(%)','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <input type="text" name="fee" id="fee" style="width:100%"  value="<?= $alliance->fee; ?>"<?= ($alliance->status == 1) ? 'required' : 'readonly'; ?>>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Fee(%)','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <input type="text" name="fee" id="fee" style="width:100%"  value="10" required>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php if(isset($alliance) && !empty($alliance)): ?>
                                            <th colspan="3">
                                                <label for="input_id"><b><?= __('Address','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <textarea name="address" type="text" style="width:100%;height:100px;resize:none;" <?= ($alliance->status == 1) ? 'required' : 'readonly'; ?>><?=  (!empty($alliance->address)) ? $alliance->address : ''; ?></textarea>
                                            </th>
                                        <?php else: ?>
                                            <th colspan="3">
                                                <label for="input_id"><b><?= __('Address','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <textarea name="address" type="text" style="width:100%;height:100px;resize:none;" required></textarea>
                                            </th>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <?php if(isset($alliance) && !empty($alliance)): ?>
                                            <th colspan="3">
                                                <label for="input_id"><b><?= __('Description','edusystem'); ?></b><?= ($alliance->status == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                                <textarea name="description" type="text" style="width:100%;height:100px;resize:none;" <?= ($alliance->status == 1) ? 'required' : 'readonly'; ?>><?=  (!empty($alliance->description)) ? $alliance->description : ''; ?></textarea>
                                            </th>
                                        <?php else: ?>
                                            <th colspan="3">
                                                <label for="input_id"><b><?= __('Description','edusystem'); ?></b><span class="text-danger">*</span></label><br>
                                                <textarea name="description" type="text" style="width:100%;height:100px;resize:none;" required></textarea>
                                            </th>
                                        <?php endif; ?>
                                    </tr>
                                </tbody>
                            </table>
                            <?php if(isset($alliance) && !empty($alliance)): ?>
                                <?php if($alliance->status == 0): ?>
                                    <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                        <button type="button" data-id="1" data-alliance="<?= $alliance->id; ?>" data-title="<?= __('Approve Alliance','edusystem'); ?>" data-message="<?= __('Do you want to approve this alliance?','edusystem'); ?>" class="button button-primary change-status-alliance"><?= __('Approve','edusystem'); ?></button>
                                        <button type="button" data-id="2" data-alliance="<?= $alliance->id; ?>" data-title="<?= __('Decline Alliance','edusystem'); ?>" data-message="<?= __('Do pou you want to decline this alliance?','edusystem'); ?>" class="button button-danger change-status-alliance"><?= __('Declined','edusystem'); ?></button>
                                    </div>
                                <?php endif; ?>
                                <?php if($alliance->status == 1){ ?>
                                    <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                        <button type="submit" class="button button-primary"><?= __('Save Changes','edusystem'); ?></button>
                                    </div>
                                <?php } ?>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit" class="button button-primary"><?= __('Add Alliance','edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if(isset($alliance) && !empty($alliance)): ?>
        <?php if($alliance->status == 1): ?>
            <h2 style="margin-bottom:15px;"><?= __('Registered institutes','edusystem'); ?></h2>

            <div id="dashboard-widgets" class="metabox-holder">
                <div id="postbox-container-1" style="width:100% !important;">
                    <div id="normal-sortables">
                        <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                            <div class="inside">
                            <table id="table-products" class="wp-list-table widefat fixed posts striped" style="margin-top:20px;">
                                <thead>
                                    <tr>
                                        <th scope="col" class="manage-column column-primary column-title"><?= __('Institute name','edusystem') ?></th>
                                        <th scope="col" class="manage-column column-title-status"><?= __('Status','edusystem') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($institutes) && !empty($institutes)): ?>
                                        <?php foreach($institutes as $institute): ?>
                                            <tr>
                                                <td class="column-primary" data-colname="<?= __('Institute name','edusystem'); ?>">
                                                    <?= $institute->name; ?>
                                                    <button type="button" class="toggle-row"><span class="screen-reader-text"></span></button>
                                                </td>
                                                <td class="column" data-colname="<?= __('Status','edusystem'); ?>"><?= get_name_status_institute($institute->status); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2" style="text-align:center;"><?= __('There are no registered institutions','edusystem'); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>
<?php 
    include(plugin_dir_path(__FILE__).'modal-status-alliance.php');
    include(plugin_dir_path(__FILE__).'modal-delete-alliance.php');
?>
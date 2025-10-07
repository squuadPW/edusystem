<div class="wrap">
    <h2 class="staff-title"><?= __('Staff Details', 'edusystem'); ?></h2>

    <?php if(isset($_COOKIE['message']) && !empty($_COOKIE['message'])){ ?>
        <div class="notice notice-success is-dismissible"><p><?= $_COOKIE['message']; ?></p></div>
        <?php setcookie('message','',time(),'/'); ?>
    <?php } ?>
    <?php if(isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])){ ?>
        <div class="notice notice-error is-dismissible"><p><?= $_COOKIE['message-error']; ?></p></div>
        <?php setcookie('message-error','',time(),'/'); ?>
    <?php } ?>

    <div class="staff-toolbar">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_staff_content'); ?>"><?= __('Back', 'edusystem'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder staff-content">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox staff-form">
                    <div class="inside">
                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_staff_content&action=save_staff_details'); ?>">
                            <table class="form-table staff-table">
                                <tbody>
                                    <tr>
                                        <td style="font-weight:400;">
                                            <?php if (isset($staff) && !empty($staff)): ?>
                                                <label for="input_id"><b><?= __('User login', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="user_login"
                                                    value="<?= $staff->user_login; ?>" readonly>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('User login', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="user_login" value="" required>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight:400;">
                                            <?php if (isset($staff) && !empty($staff)): ?>
                                                <label for="input_id"><b><?= __('Email', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="email"
                                                    value="<?= $staff->user_email; ?>">
                                                <input type="hidden" name="staff_id" id="staff_id"
                                                    value="<?= $staff->ID; ?>">
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Email', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="email" value="" required>
                                                <input type="hidden" name="staff_id" id="staff_id" value="">
                                            <?php endif; ?>
                                        </td>

                                        <td style="font-weight:400;">
                                            <label
                                                for="input_id"><b><?= __('Password', 'edusystem'); ?></b><?php !$staff ? '<span class="text-danger">*</span>' : '' ?></label><br>
                                            <input type="password" name="password" value="" <?php !$staff ? 'required' : '' ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight:400;">
                                            <?php if (isset($staff) && !empty($staff)): ?>
                                                <label for="input_id"><b><?= __('First name', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="first_name"
                                                    value="<?= get_user_meta($staff->ID, 'first_name', true); ?>">
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('First name', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="first_name" value="" required>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight:400;">
                                            <?php if (isset($staff) && !empty($staff)): ?>
                                                <label for="input_id"><b><?= __('First surname', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="last_name"
                                                    value="<?= get_user_meta($staff->ID, 'last_name', true); ?>">

                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('First surname', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="last_name" value="" required>

                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight:400;">
                                            <?php if (isset($staff) && !empty($staff)): ?>
                                                <label for="input_id"><b><?= __('Display name', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="display_name" value="<?= $staff->display_name; ?>">

                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Display name', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="display_name" value="" required>

                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight:400;">
                                            <?php if (isset($staff) && !empty($staff)): ?>
                                                <label for="input_id"><b><?= __('Roles', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <?php
                                                $user_data = get_userdata($staff->ID);
                                                $roles_user = $user_data->roles;
                                                $all_roles = wp_roles()->get_names();
                                                ?>

                                                <select multiple="multiple" name="user_roles[]">
                                                    <?php foreach ($all_roles as $role => $label): ?>
                                                        <option value="<?php echo $role; ?>" <?php echo in_array($role, $roles_user) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Roles', 'edusystem'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <?php
                                                $roles = wp_roles()->get_names();
                                                ?>

                                                <select multiple="multiple" name="user_roles[]">
                                                    <?php foreach ($roles as $role => $label): ?>
                                                        <option value="<?php echo $role; ?>"><?php echo $label; ?></option>
                                                    <?php endforeach; ?>
                                                </select>

                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight:400;">
                                            <?php if (isset($staff) && !empty($staff)): ?>
                                                <?php
                                                    $manager_user_id = get_user_meta( $staff->ID, 'manager_user_id', true );
                                                ?>
                                                <label for="input_id"><b><?= __('Manager', 'edusystem'); ?></b></label><br>
                                                    <select name="manager_user_id" style="width: 100%">
                                                        <option value="">Select an option</option>
                                                        <?php foreach ($managers as $manager): ?>
                                                            <option value="<?= esc_attr($manager->ID) ?>" 
                                                                <?= $manager->ID == $manager_user_id ? 'selected' : ''; ?>>
                                                                <?= esc_html($manager->first_name) ?> <?= esc_html($manager->last_name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                            <?php else: ?>
                                                <label for="input_id"><b><?= __('Managers', 'edusystem'); ?></b></label><br>
                                                    <select name="manager_user_id" style="width: 100%">
                                                        <option value="">Select an option</option>
                                                        <?php foreach ($managers as $manager): ?>
                                                            <option value="<?= esc_attr($manager->ID) ?>" >
                                                                <?= esc_html($manager->first_name) ?> <?= esc_html($manager->last_name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php if (isset($staff) && !empty($staff)): ?>
                                <div class="staff-actions">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Save changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div class="staff-actions">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add staff', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .staff-module {
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .staff-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .staff-notice {
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 10px;
    }

    .staff-toolbar {
        margin-bottom: 10px;
    }

    .staff-content {
        padding: 20px;
    }

    .staff-form {
        background-color: #fff;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .staff-subtitle {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .staff-table {
        width: 100%;
        border-collapse: collapse;
    }

    .staff-actions {
        margin-top: 20px;
        text-align: right;
    }

    .staff-actions button {
        margin-left: 10px;
    }

    input,
    select {
        width: 100%;
    }
</style>
<div class="wrap">

    <?php if (isset($_COOKIE['message']) && !empty($_COOKIE['message'])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?= $_COOKIE['message']; ?></p>
        </div>
        <?php setcookie('message', '', time(), '/'); ?>
    <?php endif; ?>

    <?php if (isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])): ?>
        <div class="notice notice-error is-dismissible">
            <p><?= $_COOKIE['message-error']; ?></p>
        </div>
        <?php setcookie('message-error', '', time(), '/'); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3><?= __('Scholarship Information', 'edusystem'); ?></h3>
        </div>
        <div class="card-body">

            <form method="post"
                action="<?= admin_url('admin.php?page=add_admin_form_available_scholarships_content&action=save_scholarship'); ?>">
                <div>
                    <div class="form-group">
                        <div class="form-group" style="text-align: center; margin-top: 20px">
                            <input type="checkbox" name="is_active" id="is_active" <?= $scholarship ? (($scholarship->is_active == 1) ? 'checked' : '') : 'checked'; ?>>
                            <label for="is_active"><b><?= __('Active', 'edusystem'); ?></b></label>
                        </div>
                        <input type="hidden" name="scholarship_id" id="scholarship_id" value="<?= $scholarship->id; ?>">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" value="<?= $scholarship ? $scholarship->name : '' ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea type="text" name="description"
                                required><?= $scholarship ? $scholarship->description : '' ?></textarea>
                        </div>
                        <h3
                            style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                            <b><?= __('This SCHOLARSHIP applies to:', 'edusystem'); ?></b>
                        </h3>
                        <div class="grid-container-report-3">
                            <div class="form-group" style="text-align: center">
                                <input type="checkbox" name="fee_registration" id="fee_registration" <?= $scholarship ? (($scholarship->fee_registration == 1) ? 'checked' : '') : ''; ?>>
                                <label for="fee_registration"><b><?= __('Fee registration', 'edusystem'); ?></b></label>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <input type="checkbox" name="program" id="program" <?= $scholarship ? (($scholarship->program == 1) ? 'checked' : '') : ''; ?>>
                                <label for="program"><b><?= __('Program', 'edusystem'); ?></b></label>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <input type="checkbox" name="fee_graduation" id="fee_graduation" <?= $scholarship ? (($scholarship->fee_graduation == 1) ? 'checked' : '') : ''; ?>>
                                <label for="fee_graduation"><b><?= __('Fee graduation', 'edusystem'); ?></b></label>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <label for="percent_registration">Percent (%)</label>
                                <input type="numer" step="0" name="percent_registration" value="<?= $scholarship ? $scholarship->percent_registration : '' ?>" required>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <label for="percent_program">Percent (%)</label>
                                <input type="numer" step="0" name="percent_program" value="<?= $scholarship ? $scholarship->percent_program : '' ?>" required>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <label for="percent_graduation">Percent (%)</label>
                                <input type="numer" step="0" name="percent_graduation" value="<?= $scholarship ? $scholarship->percent_graduation : '' ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($scholarship) && !empty($scholarship)): ?>
                    <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                        <a class="button button-outline-primary"
                            href="<?= admin_url('admin.php?page=add_admin_form_available_scholarships_content'); ?>"><?= __('Exit', 'edusystem'); ?></a>
                        <button type="submit" class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                    </div>
                <?php else: ?>
                    <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                        <a class="button button-outline-primary"
                            href="<?= admin_url('admin.php?page=add_admin_form_available_scholarships_content'); ?>"><?= __('Exit', 'edusystem'); ?></a>
                        <button type="submit" class="button button-primary"><?= __('Add scholarship', 'edusystem'); ?></button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
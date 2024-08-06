<div class="wrap">
    <?php if(isset($period) && !empty($period)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Period Details','aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Add Period','aes'); ?></h2>
    <?php endif; ?>

    <?php if(isset($_COOKIE['message']) && !empty($_COOKIE['message'])){ ?>
        <div class="notice notice-success is-dismissible"><p><?= $_COOKIE['message']; ?></p></div>
        <?php setcookie('message','',time(),'/'); ?>
    <?php } ?>
    <?php if(isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])){ ?>
        <div class="notice notice-error is-dismissible"><p><?= $_COOKIE['message-error']; ?></p></div>
        <?php setcookie('message-error','',time(),'/'); ?>
    <?php } ?>
    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=add_admin_form_academic_periods_content'); ?>"><?= __('Back','aes'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post" action="<?= admin_url('admin.php?page=add_admin_form_academic_periods_content&action=save_period_details'); ?>">
                            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center;"><b><?= __('Period Information','aes'); ?></b></h3>
                            <table class="form-table table-customize" style="margin-top:0px;">
                            <table class="form-table">
                                <tbody>
                                <tr>
                                        <td style="font-weight:400;">
                                            <?php if(isset($period) && !empty($period)): ?>
                                                <input type="hidden" name="period_id" id="period_id" value="<?= $period->id; ?>">
                                                <label for="status_id"><b><?= __('Active','aes'); ?></b></label><br>
                                                <input type="checkbox" name="status_id" id="status_id" value="1" <?= ($period->status_id == 1) ? 'checked' : ''; ?>>
                                            <?php else: ?>
                                                <input type="hidden" name="period_id" id="period_id" value="">
                                                <label for="status_id"><b><?= __('Active','aes'); ?></b></label><br>
                                                <input type="checkbox" name="status_id" id="status_id" value="1" checked>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <tr>
                                    <td style="font-weight:400;">
                                        <?php if(isset($period) && !empty($period)): ?>
                                            <label for="input_id"><b><?= __('Name','aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="name"  value="<?= ucwords($period->name); ?>"> 
                                            <input type="hidden" name="period_id" id="period_id" value="<?= $period->id; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Name','aes'); ?></b><span class="text-danger">*</span></label><br>
                                            <input type="text" name="name"  value="" required> 
                                            <input type="hidden" name="period_id" id="period_id" value="">
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-weight:400;">
                                        <?php if(isset($period) && !empty($period)): ?>
                                            <label for="input_id"><b><?= __('Code','aes'); ?></b><?= ($period->status_id == 1) ? '<span class="text-danger">*</span>' : ''; ?></label><br>
                                            <input type="text" name="code"  value="<?= ucwords($period->code); ?>"> 
                                            <input type="hidden" name="period_id" id="period_id" value="<?= $period->id; ?>">
                                        <?php else: ?>
                                            <label for="input_id"><b><?= __('Code','aes'); ?></b><span class="text-danger">*</span></label><br>
                                            <input type="text" name="code"  value="" required> 
                                            <input type="hidden" name="period_id" id="period_id" value="">
                                        <?php endif; ?>
                                    </td>
                                    </tr>

                                </tbody>
                            </table>

                            <?php if(isset($period) && !empty($period)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit" class="button button-primary"><?= __('Saves changes','aes'); ?></button>
                                </div>
                            <?php else: ?>
                                    <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                        <button type="submit" class="button button-primary"><?= __('Add Period','aes'); ?></button>
                                    </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

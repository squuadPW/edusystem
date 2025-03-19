<style>
    .form-table th {
        width: auto !important;
    }
</style>

<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Request details','edusystem'); ?></h2>
    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>
    <div id="notice-request-completed" style="display:none;" class="notice notice-info"><p><?= __('Request Completed','edusystem'); ?></p></div>
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row" ><label for="input_id"><?= __('Request ID','edusystem').':'; ?></label></th>
                                    <td class="text-uppercase">
                                        <?= $request->id . ' - ' . get_type_request_details($request->type_id)->type; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" ><label for="input_id"><?= __('Partner','edusystem').':'; ?></label></th>
                                    <td class="text-uppercase">
                                        <?= strtoupper($partner->first_name) . ' ' . strtoupper($partner->last_name); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" ><label for="input_id"><?= __('Student','edusystem').':'; ?></label></th>
                                    <td class="text-uppercase">
                                        <?php if ($student) { ?>
                                            <?= strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ' ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name); ?>
                                        <?php } else { ?>
                                            N/A
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" ><label for="input_id"><?= __('Description','edusystem').':'; ?></label></th>
                                    <td>
                                        <?= $request->description; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" ><label for="input_id"><?= __('Status','edusystem').':'; ?></label></th>
                                    <td>
                                        <?= get_request_status($request->status_id); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <?php if($request->status_id == 0) { ?>
                            <div style="margin-top:10px;display:flex;flex-direction:row;width:100%;justify-content:end;">
                                <div style="margin-right: 10px">
                                    <?php if(wp_is_mobile()){ ?>
                                        <button data-message="<?= __('Do you want to decline this request?','edusystem'); ?>" data-title="<?= __('Decline','edusystem'); ?>" data-action="<?= 2; ?>" id="decline_request" style="width:100%;" class="button button-danger"><?= __('Decline','edusystem'); ?></button>
                                    <?php }else{ ?>
                                        <button data-message="<?= __('Do you want to decline this request?','edusystem'); ?>" data-title="<?= __('Decline','edusystem'); ?>" data-action="<?= 2; ?>" id="decline_request" class="button button-danger"><?= __('Decline','edusystem'); ?></button>
                                    <?php } ?>
                                </div>
                                <div>
                                    <?php if(wp_is_mobile()){ ?>
                                        <button data-message="<?= __('Do you want to approve this request?','edusystem'); ?>" data-title="<?= __('Approve','edusystem'); ?>" data-action="<?= 3; ?>" id="approved_request" style="width:100%;" class="button button-success"><?= __('Approve','edusystem'); ?></button>
                                    <?php }else{ ?>
                                        <button data-message="<?= __('Do you want to approve this request?','edusystem'); ?>" data-title="<?= __('Approve','edusystem'); ?>" data-action="<?= 3; ?>" id="approved_request" class="button button-success"><?= __('Approve','edusystem'); ?></button>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    include(plugin_dir_path(__FILE__).'modal-status-request.php');
?>
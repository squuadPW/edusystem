<?php

global $wpdb;
$current_user = wp_get_current_user();
$table_students = $wpdb->prefix . 'students';
$student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}' OR partner_id={$current_user->ID}");
?>

<?php if (!$student->moodle_password) { ?>
    <div class="text-center info-box">
        <h2 style="font-size:24px;text-align:center;"><?= __('Documents', 'edusystem'); ?></h2>
        <p><?= __('To access the', 'edusystem'); ?> <a style="text-decoration: underline !important; color: #002fbd;"
                href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student' ?>"><?= __('virtual classroom', 'edusystem') ?></a>,
            <?= __('please ensure you complete the following steps', 'edusystem') ?>:</p>
        <ul class="info-list">
            <li>
                <i class="fas fa-upload"></i>
                <?= __('Once your payment is approved, the option to upload all required documents is enabled in your registration form.', 'edusystem'); ?>
            </li>
            <li>
        </ul>
        <!-- <p class="info-note">Once both steps are complete, you will receive an email with instructions on how to access the virtual classroom. Please note that access will only be granted once all required documents have been received and your payment has been processed.</p> -->
    </div>
<?php } else { ?>
    <h2 style="font-size:24px;text-align:center;"><?= __('Documents', 'edusystem'); ?></h2>
<?php } ?>

<style>
    container */ img {
        max-width: 100%;
        /* This rule is very important, please do not ignore this! */
    }

    #canvas-crop-document {
        height: 600px;
        width: 600px;
        background-color: #ffffff;
        cursor: default;
        border: 1px solid black;
    }
</style>


<?php if (!empty($students)): ?>
    <form id="send-documents-student" method="post"
        action="<?= wc_get_endpoint_url('student-documents', '', get_permalink(get_option('woocommerce_myaccount_page_id'))) . '?actions=save_documents'; ?>"
        enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_documents">
        <?php foreach ($students as $student): ?>
            <input type="hidden" name="students[]" value="<?= $student->id; ?>">
            <table
                class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
                style="margin-top:20px;">
                <caption style="text-align:start;">
                    <?= __('Documents of', 'edusystem'); ?> <?= $student->name . ' ' . $student->last_name; ?>
                </caption>
                <thead>
                    <tr>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-document"><span
                                class="nobr"><?= __('Document', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span
                                class="nobr"><?= __('Status', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-action"><span
                                class="nobr"><?= __('Action', 'edusystem'); ?></span></th>
                </thead>
                <tbody>
                    <?php $documents = get_documents($student->id); ?>
                    <?php foreach ($documents as $document): ?>
                        <?php if ($document->is_visible) { ?>
                            <?php $name = get_name_document($document->document_id); ?>
                            <?php $document_name_complete = get_name_document($document->document_id); ?>
                            <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                                <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                    data-title="<?= __('Document', 'edusystem'); ?>" style="max-width: 250px;">

                                    <input type="hidden" name="<?= 'file_student_' . $student->id . '_id[]'; ?>"
                                        value="<?= $document->id; ?>">
                                    <?php if ($document->is_required): ?>
                                        <?php $name = $name . "<span class='required' style='font-size:24px;'>*</span>"; ?>
                                    <?php endif; ?>

                                    <?= $name; ?>
                                    <?php if ($document->max_date_upload): ?>
                                        <span class="deadline">- <?= __('DEADLINE', 'edusystem') ?>: <?= date('m/d/Y', strtotime($document->max_date_upload)) ?></span>
                                    <?php endif; ?>

                                    <span class="help-tooltip"
                                        data-tippy-content="<?php echo get_help_info_document($document->document_id) ?>">
                                        <span style="color: #002fbd; margin-top: -5px;" class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </td>
                                <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date"
                                    data-title="<?= __('Status', 'edusystem'); ?>">
                                    <input type="hidden" name="<?= 'status_file_' . $document->id . '_student_id_' . $student->id; ?>"
                                        value="<?= $document->status; ?>">
                                    <input type="hidden"
                                        name="<?= 'file_is_required' . $document->id . '_student_id_' . $student->id; ?>"
                                        value="<?= $document->is_required; ?>">
                                    <?php
                                    $status = get_status_document($document->status);
                                    $style = '';
                                    switch ($status) {
                                        case __('Sent', 'edusystem'):
                                        case __('Processing', 'edusystem'):
                                            $style = 'color: blue';
                                            break;
                                        case __('Declined', 'edusystem'):
                                        case __('Waiting update', 'edusystem'):
                                        case __('Expired', 'edusystem'):
                                            $style = 'color: red';
                                            break;
                                        case __('Approved', 'edusystem'):
                                            $style = 'color: green';
                                            break;
                                    }
                                    ?>
                                    <span style="<?= $style ?>"><?= $status == __('No sent', 'edusytem') ? __('Pending', 'edusystem') : $status ?></span>
                                </td>
                                <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                    data-title="<?= __('Action', 'edusystem'); ?>">
                                    <?php if ($document->status == 0 || $document->status == 3 || $document->status == 4 || $document->status == 6) { ?>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" <?= in_array($document_name_complete, $arr_photos_student) ? 'id=student_photo' : '' ?>
                                                name="<?= 'document_' . $document->id . '_student_id_' . $student->id; ?>"
                                                accept="<?php echo get_type_file_document($document->document_id) ?>"
                                                data-fileallowed="<?php echo get_type_file_document($document->document_id) ?>">
                                            <span class="custom-file-label" <?= in_array($document_name_complete, $arr_photos_student) ? 'id=student_photo_label_input' : '' ?>><?= __('Select file', 'edusystem') ?></span>
                                        </div>
                                    <?php } else { ?>
                                        <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>" type="button"
                                            class="button"><?= __('View Document', 'edusystem') ?> </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } else if ($document->status != 0 && $document->status != 3) { ?>
                                <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                        data-title="<?= __('Document', 'edusystem'); ?>">
                                        <input type="hidden" name="<?= 'file_student_' . $student->id . '_id[]'; ?>"
                                            value="<?= $document->id; ?>">
                                    <?php $name = get_name_document($document->document_id); ?>

                                        <strong><?= $name; ?></strong>
                                    <?php if ($document->max_date_upload): ?>
                                            <span class="deadline">- <?= __('DEADLINE', 'edusystem') ?>:
                                            <?= date('m/d/Y', strtotime($document->max_date_upload)) ?></span>
                                    <?php endif; ?>
                                    </td>
                                    <?php
                                    $status = get_status_document($document->status);
                                    $style = '';
                                    switch ($status) {
                                        case __('Sent', 'edusystem'):
                                        case __('Processing', 'edusystem'):
                                            $style = 'color: blue';
                                            break;
                                        case __('Declined', 'edusystem'):
                                        case __('Waiting update', 'edusystem'):
                                        case __('Expired', 'edusystem'):
                                            $style = 'color: red';
                                            break;
                                        case __('Approved', 'edusystem'):
                                            $style = 'color: green';
                                            break;
                                    }
                                    ?>

                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date"
                                        data-title="<?= __('Status', 'edusystem'); ?>">
                                        <span style="<?= $style ?>"><?= $status ==  __('No sent', 'edusytem') ? __('Pending', 'edusystem') : $status ?></span>
                                    </td>
                                    <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                        data-title="<?= __('Action', 'edusystem'); ?>">
                                        <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>" type="button"
                                            class="button"><?= __('View Document', 'edusystem') ?></a>
                                    </td>
                                </tr>
                        <?php } ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <div style="display:block;text-align:center;">
            <button class="submit" type="submit" style="display: none"
                id="send_real"><?= __('Send Documents', 'edusystem'); ?></button>
            <div id="progressButton">
                <div id="progressBar"></div>
                <div id="buttonText"><?= __('Send Documents', 'edusystem') ?></div>
            </div>
        </div>
    </form>
<?php endif; ?>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<link href="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.css" rel="stylesheet">
<script src="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.js"></script>

<?php
include('modal-cropperjs.php');
?>
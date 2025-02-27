<?php

global $wpdb;
$current_user = wp_get_current_user();
$table_students = $wpdb->prefix . 'students';
$student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE email='{$current_user->user_email}' OR partner_id={$current_user->ID}");
?>

<?php if (!$student->moodle_password) { ?>
    <div class="text-center info-box">
        <h2 style="font-size:24px;text-align:center;"><?= __('Documents', 'aes'); ?></h2>
        <p>To access the <a style="text-decoration: underline !important; color: #002fbd;"
                href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student' ?>">virtual
                classroom</a>, please ensure you complete the following steps:</p>
        <ul class="info-list">
            <li>
                <i class="fas fa-upload"></i>
                Once your payment is approved, the option to upload all required documents is enabled in your registration
                form.
            </li>
            <li>
        </ul>
        <!-- <p class="info-note">Once both steps are complete, you will receive an email with instructions on how to access the virtual classroom. Please note that access will only be granted once all required documents have been received and your payment has been processed.</p> -->
    </div>
<?php } else { ?>
    <h2 style="font-size:24px;text-align:center;"><?= __('Documents', 'aes'); ?></h2>
<?php } ?>


<?php if (!empty($students)): ?>

    <form method="post"
        action="<?= wc_get_endpoint_url('student-documents', '', get_permalink(get_option('woocommerce_myaccount_page_id'))) . '?actions=save_documents'; ?>"
        enctype="multipart/form-data">
        <?php foreach ($students as $student): ?>
            <input type="hidden" name="students[]" value="<?= $student->id; ?>">
            <table
                class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
                style="margin-top:20px;">
                <caption style="text-align:start;">
                    Documents of <?= $student->name . ' ' . $student->last_name; ?>
                </caption>
                <thead>
                    <tr>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-document"><span
                                class="nobr"><?= __('Document', 'aes'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span
                                class="nobr"><?= __('Status', 'aes'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-action"><span
                                class="nobr"><?= __('action', 'aes'); ?></span></th>
                </thead>
                <tbody>
                <?php print_r(get_type_file_document($document->document_id)) ?>
                    <?php $documents = get_documents($student->id); ?>
                    <?php foreach ($documents as $document): ?>
                        <?php if ($document->is_visible) { ?>
                            <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                                <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                    data-title="<?= __('Document', 'aes'); ?>">
                                    <input type="hidden" name="<?= 'file_student_' . $student->id . '_id[]'; ?>"
                                        value="<?= $document->id; ?>">
                                    <?php $name = get_name_document($document->document_id); ?>
                                    <?php if ($document->is_required): ?>
                                        <?php $name = $name . "<span class='required' style='font-size:24px;'>*</span>"; ?>
                                    <?php endif; ?>

                                    <?= $name; ?>

                                    <span class="help-tooltip" data-tippy-content="<?php echo get_help_info_document($document->document_id) ?>">
                                        <span style="color: #002fbd; margin-top: -5px;" class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </td>
                                <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date"
                                    data-title="<?= __('Status', 'aes'); ?>">
                                    <input type="hidden" name="<?= 'status_file_' . $document->id . '_student_id_' . $student->id; ?>"
                                        value="<?= $document->status; ?>">
                                    <input type="hidden" name="<?= 'file_is_required' . $document->id . '_student_id_' . $student->id; ?>"
                                        value="<?= $document->is_required; ?>">
                                    <?= $status = get_status_document($document->status); ?>
                                </td>
                                <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                    data-title="<?= __('Action', 'aes'); ?>">
                                    <?php if ($document->status == 0 || $document->status == 3 || $document->status == 4) { ?>
                                        <div class="custom-file">
                                        <input type="file" class="custom-file-input"
                                            name="<?= 'document_' . $document->id . '_student_id_' . $student->id; ?>" accept="<?php echo get_type_file_document($document->document_id) ?>" data-fileallowed="<?php echo get_type_file_document($document->document_id) ?>">
                                            <span class="custom-file-label">Select file</span>
                                        </div>
                                    <?php } else { ?>
                                        <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>" type="button"
                                            class="button">View Document</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } else if($document->status != 0 && $document->status != 3) { ?>
                            <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                                <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                    data-title="<?= __('Document', 'aes'); ?>">
                                    <input type="hidden" name="<?= 'file_student_' . $student->id . '_id[]'; ?>"
                                        value="<?= $document->id; ?>">
                                    <?php $name = get_name_document($document->document_id); ?>

                                    <strong><?= $name; ?> AGREEMENT</strong>
                                </td>
                                <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date"
                                    data-title="<?= __('Status', 'aes'); ?>">
                                    <?= $status = get_status_document($document->status); ?>
                                </td>
                                <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                                    data-title="<?= __('Action', 'aes'); ?>">
                                    <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>" type="button"
                                    class="button">View Document</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
        <div style="display:block;text-align:center;">
            <button class="submit" type="submit"><?= __('Send Documents', 'aes'); ?></button>
        </div>
    </form>
<?php endif; ?>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
      // With the above scripts loaded, you can call `tippy()` with a CSS
      // selector and a `content` prop:
      tippy('.help-tooltip', {
        allowHTML: true
      });
</script>
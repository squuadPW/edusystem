<?php

global $wpdb;
$current_user = wp_get_current_user();
$table_teachers = $wpdb->prefix . 'teachers';
$teacher = get_teacher_details($current_user->user_email);
?>

<h2 style="font-size:24px;text-align:center;"><?= __('Documents', 'aes'); ?></h2>

<form method="post"
    action="<?= wc_get_endpoint_url('teacher-documents', '', get_permalink(get_option('woocommerce_myaccount_page_id'))) . '?actions=save_documents_teacher'; ?>"
    enctype="multipart/form-data">

    <input type="hidden" name="teachers[]" value="<?= $teacher->id; ?>">
    <table
        class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
        style="margin-top:20px;">
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
            <?php $documents = get_teacher_documents($teacher->id); ?>
            <?php foreach ($documents as $document): ?>
                <?php if ($document->is_visible) { ?>
                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                            data-title="<?= __('Document', 'aes'); ?>">
                            <input type="hidden" name="<?= 'file_teacher_' . $teacher->id . '_id[]'; ?>"
                                value="<?= $document->id; ?>">
                            <?php $name = get_name_document($document->document_id); ?>

                            <?php if ($document->is_required): ?>
                                <?php $name = $name . "<span class='required' style='font-size:24px;'>*</span>"; ?>
                            <?php endif; ?>

                            <?= $name; ?>

                            <span class="help-tooltip"
                                data-tippy-content="<?php echo get_help_info_document($document->document_id) ?>">
                                <span style="color: #002fbd; margin-top: -5px;" class="dashicons dashicons-editor-help"></span>
                            </span>
                        </td>
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date"
                            data-title="<?= __('Status', 'aes'); ?>">
                            <input type="hidden" name="<?= 'status_file_' . $document->id . '_teacher_id_' . $teacher->id; ?>"
                                value="<?= $document->status; ?>">
                            <input type="hidden"
                                name="<?= 'file_is_required' . $document->id . '_teacher_id_' . $teacher->id; ?>"
                                value="<?= $document->is_required; ?>">
                            <?= $status = get_status_document($document->status); ?>
                        </td>
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"
                            data-title="<?= __('Action', 'aes'); ?>">
                            <?php if ($document->status == 0 || $document->status == 3 || $document->status == 4) { ?>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input"
                                        name="<?= 'document_' . $document->id . '_teacher_id_' . $teacher->id; ?>"
                                        accept="<?php echo get_type_file_document_teacher($document->document_id) ?>"
                                        data-fileallowed="<?php echo get_type_file_document_teacher($document->document_id) ?>">
                                    <span class="custom-file-label">Select file</span>
                                </div>
                            <?php } else { ?>
                                <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>" type="button"
                                    class="button">View Document</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="display:block;text-align:center;">
        <button class="submit" type="submit"><?= __('Send Documents', 'aes'); ?></button>
    </div>
</form>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
    // With the above scripts loaded, you can call `tippy()` with a CSS
    // selector and a `content` prop:
    tippy('.help-tooltip', {
        allowHTML: true
    });
</script>
<h2 style="font-size:24px;text-align:center;"><?= __('Documents','form-plugin'); ?></h2>

<?php if(!empty($students)): ?>

<form method="post" action="<?= wc_get_endpoint_url('student-documents', '', get_permalink(get_option('woocommerce_myaccount_page_id'))).'?actions=save_documents'; ?>" enctype="multipart/form-data">
    <?php foreach($students as $student): ?>
        <input type="hidden" name="students[]" value="<?= $student->id; ?>">
        <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table" style="margin-top:20px;">
            <caption style="text-align:start;">
                <?= $student->name.' '.$student->last_name; ?>
            </caption>
            <thead>
                <tr>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-document"><span class="nobr"><?= __('Document','form-plugin'); ?></span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?= __('Status','form-plugin'); ?></span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-action"><span class="nobr"><?= __('action','form-plugin'); ?></span></th>
            </thead>
             <tbody>
                <?php $documents = get_documents($student->id); ?>
                <?php foreach($documents as $document): ?>
                    <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?= __('Document','form-plugin'); ?>">
                            <input type="hidden" name="<?= 'file_student_'.$student->id.'_id[]'; ?>" value="<?= $document->document_id; ?>">
                            <?php
                                $name = match ($document->document_id) {
                                    'certified_notes_high_school' => __('CERTIFIED NOTES HIGH SCHOOL','form-plugin'),
                                    'high_school_diploma' => __('HIGH SCHOOL DIPLOMA','form-plugin'),
                                    'id_parents' => __('ID OR CI OF THE PARENTS','form-plugin'),
                                    'id_student' => __('ID STUDENTS','form-plugin'),
                                    'photo_student_card' => __('PHOTO OF STUDENT CARD','form-plugin'),
                                    'proof_of_grades' => __('PROOF OF GRADE','form-plugin'),
                                    'proof_of_study' => __('PROOF OF STUDY','form-plugin'),
                                    'vaccunation_card' => __('VACCUNATION CARD','form-plugin'),
                                }
                            ?>

                            <?php if($document->document_id == 'id_student'): ?>
                                <?= $name."<span class='required' style='font-size:24px;'>*</span>"; ?>
                            <?php else: ?>
                                <?= $name; ?>
                            <?php endif; ?>
                        </td>
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="<?= __('Status','form-plugin'); ?>">
                            <input type="hidden" name="<?= 'status_file_'.$document->document_id.'_student_id_'.$student->id; ?>" value="<?= $document->status; ?>">
                            <?= 
                                $status = match ($document->status){
                                    '0' => __('No sent','form-plugin'),
                                    '1' => __('Sent','form-plugin'),
                                    '2' => __('Processing','form-plugin'),
                                    '3' => __('Declined','form-plugin'),
                                    '4' => __('Expired','form-plugin'),
                                    '5' => __('Approved','form-plugin'),
                                }
                            ?>
                        </td>
                        <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="<?= __('Action','form-plugin'); ?>">
                            <?php if($document->status == 0 || $document->status == 3 || $document->status == 4){ ?>
                                <input type="file" name="<?= 'document_'.$document->document_id.'_student_id_'.$student->id; ?>">
                            <?php }else{ ?>
                                <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>" type="button" class="button">View Document</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
    <div style="display:block;text-align:end;">
        <button class="button" type="submit"><?= __('Send Documents','form-plugin'); ?></button>
    </div>
</form>
<?php endif; ?>
<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Student Details','aes'); ?></h2>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2 style="margin-bottom:15px;"><?= __('Documents','aes'); ?></h2>

    <table id="table-products" class="wp-list-table widefat fixed posts striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-primary column-title"><?= __('Document','aes') ?></th>
                <th scope="col" class="manage-column column-title-translate"><?= __('Status','aes') ?></th>
                <th scope="col" class="manage-column column-price"><?= __('Actions','aes') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($documents)): ?>
                <?php foreach($documents as $document): ?>
                    <tr>
                        <td class="column-primary">
                            <?= 
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
                        </td>
                        <td>
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
                        <td>
                            <button class="button button-primary"><?= __('View','aes'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
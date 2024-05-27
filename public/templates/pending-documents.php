<div class="content-dashboard">
    <h4 style="font-size:20px;text-align:start;"><?= __('Pending Documents','form-plugin'); ?></h4>

    <div style="margin-top:10px;">
        <?php if(!empty($students)): ?>
           <?php foreach($students as $student): ?>
                <?php $documents = get_documents($student->id); ?>
                <?php foreach($documents as $document): ?>
                    <?php if($document->status == 0 || $document->status == 1 || $document->status == 3 || $document->status == 4): ?>
                    <?php if($document->status == 0 || $document->status == 1): ?>
                        <div class="woocommerce-info" role="alert" style="margin-bottom:10px;">
                    <?php elseif($document->status == 3): ?>
                        <div class="woocommerce-error" role="alert" style="margin-bottom:10px;">
                    <?php elseif($document->status == 4): ?>
                        <div class="woocommerce-error" role="alert" style="margin-bottom:10px;">
                    <?php endif; ?>
                            <div style="display:flex;width:100%;flex-direction:row;">
                                <span style="width:70%">

                                    <?php     
                                        $name = match ($document->document_id) {
                                            'certified_notes_high_school' => __('CERTIFIED NOTES HIGH SCHOOL','form-plugin'),
                                            'high_school_diploma' => __('HIGH SCHOOL DIPLOMA','form-plugin'),
                                            'id_parents' => __('ID OR CI OF THE PARENTS','form-plugin'),
                                            'id_student' => __('ID STUDENTS','form-plugin'),
                                            'photo_student_card' => __('PHOTO OF STUDENT CARD','form-plugin'),
                                            'proof_of_grades' => __('PROOF OF GRADE','form-plugin'),
                                            'proof_of_study' => __('PROOF OF STUDY','form-plugin'),
                                            'vaccunation_card' => __('Vaccunation_card','form-plugin'),
                                        }
                                    ?>

                                    <?= $student->name.' '.$student->last_name.' - '.$name; ?>
                                </span>
                                <span style="width:30%;text-align:end;">
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
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
       
    </div>
</div>
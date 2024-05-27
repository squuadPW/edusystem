<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Applicant details','aes'); ?></h2>

    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <table class="form-table table-customize" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><?= __('Program','aes'); ?></label><br>
                                        <select style="width:100%">
                                            <option value="aes" <?= ($student->program_id == 'aes') ? 'selected' : ''; ?>><?= __('AES (Dual Diploma)','aes'); ?></option>
                                            <option value="psp" <?= ($student->program_id == 'psp') ? 'selected' : ''; ?>><?= __('PSP (Carrera Universitaria)','aes'); ?></option>
                                            <option value="aes_psp" <?= ($student->program_id == 'aes_psp') ? 'selected' : ''; ?>><?= __('Ambos','aes'); ?></option>
                                        </select>
                                    </th>
                                    <td>
                                        <label for="input_id"><?= __('Grade','aes'); ?></label><br>
                                        <select style="width:100%" value="<?= $student->grade_id; ?>">
                                            <option value="1" <?= ($student->grade_id == 1) ? 'selected' : ''; ?>>9no (antepenúltimo)</option>
                                            <option value="2" <?= ($student->grade_id == 2) ? 'selected' : ''; ?>>10mo (penúltimo)</option>
                                            <option value="3" <?= ($student->grade_id == 3) ? 'selected' : ''; ?>>11vo (último)</option>
                                            <option value="4" <?= ($student->grade_id == 4) ? 'selected' : ''; ?>>Bachiller (graduado)</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        <table>
                        <h3 style="margin-bottom:0px;text-align:center;"><b><?= __('Personal Information','aes'); ?></b></h3>
                        <table class="form-table table-customize" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('First name','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $student->name ?>" required>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Last name','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $student->last_name; ?>" required>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('Birth date','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $student->birth_date; ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Gender','aes'); ?></b></label><br>
                                        <select style="width:100%;">
                                            <option><?= __('Female','aes'); ?></option>
                                            <option><?= __('Male','aes'); ?></option>
                                        </select>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Country','aes'); ?></b></label><br>
                                        <select style="width:100%;min-width:100%;">
                                            <option value=""></option>
                                           <?php foreach($countries as $key => $country): ?>
                                                <option value="<?= $key ?>"><?= $country; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('City','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%;"> 
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Email','aes'); ?></b></label><br>
                                        <input type="text" name="email" value="<?= $student->email; ?>" id="email" style="width:100%;">
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Phone','aes'); ?></b></label><br>
                                        <input type="text" name="email" id="email" style="width:100%;" value="<?= $student->phone; ?>">
                                    </td>
                            </tbody>
                        <table>
                        <h3 style="margin-bottom:0px;text-align:center;"><b><?= __('Partner Information','aes'); ?></b></h3>
                        <table class="form-table table-customize" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('First name','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $partner->first_name; ?>" required>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Last name','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $partner->last_name; ?>" required>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('Birth date','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Gender','aes'); ?></b></label><br>
                                        <select style="width:100%;">
                                            <option><?= __('Female','aes'); ?></option>
                                            <option><?= __('Male','aes'); ?></option>
                                        </select>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Country','aes'); ?></b></label><br>
                                        <select style="width:100%;min-width:100%;" value="<?= get_user_meta($partner->ID,'billing_country',true); ?>">
                                            <option value=""></option>
                                           <?php foreach($countries as $key => $country): ?>
                                                <option value="<?= $key ?>" <?= (get_user_meta($partner->ID,'billing_country',true) == $key) ? 'selected' : '' ?>><?= $country; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('City','aes'); ?></b></label><br>
                                        <input type="text" value="<?= get_user_meta($partner->ID,'billing_city',true) ?>" style="width:100%;"> 
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Email','aes'); ?></b></label><br>
                                        <input type="text" name="email" value="<?= get_user_meta($partner->ID,'billing_email',true) ?>" id="email" style="width:100%;">
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Phone','aes'); ?></b></label><br>
                                        <input type="text" name="email" value="<?= get_user_meta($partner->ID,'billing_phone',true) ?>"  id="email" style="width:100%;" value="">
                                    </td>
                            </tbody>
                        <table>
                            <!--
                        <div style="display:flex;width:100%;flex-direction:row;justify-content:end;">
                            <button class="button button-primary"><?= __('Updated Data','aes'); ?></button>
                        </div>
                                           -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2 style="margin-bottom:15px;"><?= __('Documents','aes'); ?></h2>
    <div id="notice-status" class="notice-custom notice-info" style="display:none;">
        <p><?= __('Status change successfully','aes'); ?></p>
    </div>
    <table id="table-products" class="wp-list-table widefat fixed posts striped" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-primary column-title"><?= __('Document','aes') ?></th>
                <th scope="col" class="manage-column column-title-translate"><?= __('Status','aes') ?></th>
                <th scope="col" class="manage-column column-price"><?= __('Actions','aes') ?></th>
            </tr>
        </thead>
        <tbody id="table-documents">
            <?php if(!empty($documents)): ?>
                <?php foreach($documents as $document): ?>
                    <tr id="<?= 'tr_document_'.$document->document_id; ?>">
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
                            <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                        </td>
                        <td id="<?= 'td_document_'.$document->document_id; ?>" data-colname="<?= __('Status','aes'); ?>">
                            <b>
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
                            </b>
                        </td>
                        <td data-colname="<?= __('Actions','aes'); ?>">
                            <?php if($document->status > 0): ?>
                                <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>" class="button button-primary"><?= __('View','aes'); ?></a>
                                <?php if($document->status != 5): ?>
                                    <button data-document-id="<?= $document->document_id; ?>" data-student-id="<?= $document->student_id; ?>" data-status="5" class="button change-status button-success"><?= __('Approved','aes'); ?></button>
                                <?php endif; ?>
                                <?php if($document->status != 5 && $document->status != 3): ?>
                                    <button data-document-id="<?= $document->document_id; ?>" data-student-id="<?= $document->student_id; ?>" data-status="3" class="button change-status button-danger"><?= __('Declined','aes'); ?></button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
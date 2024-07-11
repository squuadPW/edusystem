<div class="wrap">
    
    <?php if(in_array('institutes',$roles)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Student details','aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Applicant details','aes'); ?></h2>
    <?php endif; ?>
    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>
    <div style="display:flex;width:100%;justify-content:end;">
        <button data-id="<?= $student->id; ?>" id="button-export-xlsx" class="button button-primary"><?= __('Export Excel','aes'); ?></button>
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
                                        <label for="input_id"><b><?= __('Program','aes'); ?></b></label><br>
                                        <input type="text" value="<?= get_name_program($student->program_id); ?>" style="width:100%" readonly>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Grade','aes'); ?></b></label><br>
                                        <input type="text" value="<?= get_name_grade($student->grade_id); ?>" style="width:100%" readonly>
                                    </td>
                                </tr>
                            </tbody>
                        <table>
                        <h3 style="margin-bottom:0px;text-align:center;"><b><?= __('Personal Information','aes'); ?></b></h3>
                        <table class="form-table table-customize" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Document Type','aes'); ?></b></label><br>
                                        <input type="text" value="<?= get_name_type_document($student->type_document); ?>" style="width:100%" readonly>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('ID Document','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $student->id_document; ?>" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('First name','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $student->name ?>" readonly>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Last name','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $student->last_name; ?>" readonly>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('Birth date','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%;pointer-events:none;" class="birth_date" value="<?= $student->birth_date; ?>" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Gender','aes'); ?></b></label><br>
                                        <input type="text" name="gender" value="<?= get_gender($student->gender); ?>" style="width:100%;" readonly>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Country','aes'); ?></b></label><br>
                                        <input type="text" value="<?= get_name_country($student->country); ?>" style="width:100%;" readonly>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('City','aes'); ?></b></label><br>
                                        <input type="text" value="<?= $student->city; ?>" style="width:100%;" readonly> 
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Postal Code','aes'); ?></b></label><br>
                                        <input type="text" name="text" value="<?= $student->postal_code; ?>" style="width:100%;" readonly>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Email','aes'); ?></b></label><br>
                                        <input type="text" name="email" value="<?= $student->email; ?>" id="email" style="width:100%;" readonly>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('Phone','aes'); ?></b></label><br>
                                        <input type="text" name="email" id="email" style="width:100%;" value="<?= $student->phone; ?>" readonly>
                                    </td>
                            </tbody>
                        </table>
                        <h3 style="margin-bottom:0px;text-align:center;"><b><?= __('Partner Information','aes'); ?></b></h3>
                        <table class="form-table table-customize" style="margin-top:0px;">
                            <tbody>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Document Type','aes'); ?></b></label><br>
                                        <input type="text" value="<?= get_name_type_document($student->type_document); ?>" style="width:100%" readonly>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('ID Document','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= get_user_meta($partner->ID,'id_document',true); ?>" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('First name','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $partner->first_name; ?>" readonly>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Last name','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%" value="<?= $partner->last_name; ?>" readonly>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('Birth date','aes'); ?></b></label><br>
                                        <input type="text" style="width:100%;pointer-events:none;" class="birth_date" value="<?= get_user_meta($partner->ID,'birth_date',true); ?>"  readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Gender','aes'); ?></b></label><br>
                                        <input type="text" name="gender" value="<?= get_gender(get_user_meta($partner->ID,'gender',true)); ?>" style="width:100%;" readonly>
                                    </th>
                                    <td>
                                        <label for="input_id"><b><?= __('Country','aes'); ?></b></label><br>
                                        <input type="text" value="<?= get_name_country(get_user_meta($partner->ID,'billing_country',true)); ?>" style="width:100%;" readonly>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('City','aes'); ?></b></label><br>
                                        <input type="text" value="<?= get_user_meta($partner->ID,'billing_city',true) ?>" style="width:100%;" readonly> 
                                    </th>
                                </tr>
                                <tr>
                                    <th style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Postal Code','aes'); ?></b></label><br>
                                        <input type="text" name="text" value="<?= get_user_meta($partner->ID,'billing_postcode',true) ?>" style="width:100%;" readonly>
                                    </th>
                                    <td style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Email','aes'); ?></b></label><br>
                                        <input type="text" name="email" value="<?= get_user_meta($partner->ID,'billing_email',true) ?>" style="width:100%;" readonly>
                                    </td>
                                    <td>
                                        <label for="input_id"><b><?= __('Phone','aes'); ?></b></label><br>
                                        <input type="text" name="email" value="<?= get_user_meta($partner->ID,'billing_phone',true) ?>" style="width:100%;" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="font-weight:400;">
                                        <label for="input_id"><b><?= __('Occupation','aes'); ?></b></label><br>
                                        <input type="text" name="text" value="<?= get_user_meta($partner->ID,'occupation',true) ?>" style="width:100%;" readonly>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if(!in_array('institutes',$roles)): ?>
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
                        <tr id="<?= 'tr_document_'.$document->id; ?>">
                            <td class="column-primary">
                                <?= $name = get_name_document($document->document_id); ?>
                                <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                            </td>
                            <td id="<?= 'td_document_'.$document->document_id; ?>" data-colname="<?= __('Status','aes'); ?>">
                                <b>
                                    <?= $status = get_status_document($document->status); ?>
                                </b>
                            </td>
                            <td data-colname="<?= __('Actions','aes'); ?>">
                                <?php if($document->status > 0): ?>
                                    <a target="_blank" href="<?= wp_get_attachment_url($document->attachment_id); ?>" class="button button-primary"><?= __('View','aes'); ?></a>
                                    <?php if($document->status != 5): ?>
                                        <button data-document-id="<?= $document->id; ?>" data-student-id="<?= $document->student_id; ?>" data-status="5" class="button change-status button-success"><?= __('Approved','aes'); ?></button>
                                    <?php endif; ?>
                                    <?php if($document->status != 5 && $document->status != 3): ?>
                                        <button data-document-id="<?= $document->id; ?>" data-student-id="<?= $document->student_id; ?>" data-status="3" class="button change-status button-danger"><?= __('Declined','aes'); ?></button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
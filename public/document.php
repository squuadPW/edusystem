<?php 

add_action('woocommerce_account_teacher-documents_endpoint', function() {

    /*
        0: no enviado
        1: enviado
        2: procesando
        3: rechazado
        4 vencido
        5: aprobado
    */

    global $current_user;
    $roles = $current_user->roles;
    if(!in_array('parent',$roles) && in_array('student',$roles)){
        $student_id = get_user_meta(get_current_user_id(),'student_id',true);
        if($student_id){
            $students = get_student_from_id($student_id);
        }else{
            $students = get_student(get_current_user_id());
        }
    }

    if (in_array('parent',$roles) && in_array('student',$roles) || in_array('parent',$roles) && !in_array('student',$roles)) {
        $students = get_student(get_current_user_id());
    }
    
    include(plugin_dir_path(__FILE__).'templates/teacher-documents.php');
});

add_action('woocommerce_account_student-documents_endpoint', function() {

    /*
        0: no enviado
        1: enviado
        2: procesando
        3: rechazado
        4 vencido
        5: aprobado
    */

    global $current_user;
    $roles = $current_user->roles;
    if(!in_array('parent',$roles) && in_array('student',$roles)){
        $student_id = get_user_meta(get_current_user_id(),'student_id',true);
        if($student_id){
            $students = get_student_from_id($student_id);
        }else{
            $students = get_student(get_current_user_id());
        }
    }

    if (in_array('parent',$roles) && in_array('student',$roles) || in_array('parent',$roles) && !in_array('student',$roles)) {
        $students = get_student(get_current_user_id());
    }
    
    include(plugin_dir_path(__FILE__).'templates/documents.php');
});

function save_document() {
    if (!isset($_GET['actions']) || empty($_GET['actions'])) return;

    global $wpdb, $current_user;
    
    $action_handlers = [
        'save_documents' => 'handle_student_documents',
        'save_documents_teacher' => 'handle_teacher_documents'
    ];

    if (isset($action_handlers[$_GET['actions']])) {
        call_user_func($action_handlers[$_GET['actions']], $wpdb, $current_user);
    }

    handle_missing_documents_redirect($wpdb, $current_user);
}

function handle_student_documents($wpdb, $current_user) {
    if (empty($_POST['students'])) return;

    $tables = [
        'documents' => $wpdb->prefix.'student_documents',
        'students' => $wpdb->prefix.'students',
        'signatures' => $wpdb->prefix.'users_signatures',
        'payments' => $wpdb->prefix.'student_payments'
    ];

    $document_config = [
        'id_prefix' => 'student',
        'email_class' => 'WC_Update_Document_Email',
        'redirect_endpoint' => 'student-documents'
    ];

    process_documents($wpdb, $current_user, $tables, $document_config, $_POST['students']);
}

function handle_teacher_documents($wpdb, $current_user) {
    if (empty($_POST['teachers'])) return;

    $tables = [
        'documents' => $wpdb->prefix.'teacher_documents',
        'users' => $wpdb->prefix.'teachers',
        'redirect_endpoint' => 'teacher-documents'
    ];

    $document_config = [
        'id_prefix' => 'teacher',
        'email_class' => null,
        'redirect_endpoint' => 'teacher-documents'
    ];

    process_documents($wpdb, $current_user, $tables, $document_config, $_POST['teachers']);
}

function process_documents($wpdb, $current_user, $tables, $config, $entities) {
    foreach ($entities as $entity_id) {
        $files = $_POST["file_{$config['id_prefix']}_{$entity_id}_id"] ?? [];
        
        foreach ($files as $file_id) {
            process_single_file($wpdb, $config['id_prefix'], $entity_id, $file_id);
        }

        if ($config['email_class']) {
            handle_post_processing($wpdb, $current_user, $tables, $entity_id);
        }
    }

    wc_add_notice(__('Documents saved successfully.', 'edusystem'), 'success');
    wp_redirect(wc_get_endpoint_url(
        $config['redirect_endpoint'], 
        '', 
        get_permalink(get_option('woocommerce_myaccount_page_id'))
    ));
    exit;
}

function process_single_file($wpdb, $prefix, $entity_id, $file_id) {
    $status = $_POST["status_file_{$file_id}_{$prefix}_id_{$entity_id}"] ?? 0;
    if (!in_array($status, [0, 3, 4])) return;

    $file_temp = $_FILES["document_{$file_id}_{$prefix}_id_{$entity_id}"] ?? [];
    if (empty($file_temp['tmp_name'])) return;

    $upload_data = wp_handle_upload($file_temp, ['test_form' => false]);
    if (is_wp_error($upload_data)) return;

    $attachment_id = create_attachment($upload_data);
    if (!$attachment_id) return;

    $wpdb->update(
        $wpdb->prefix."{$prefix}_documents",
        [
            'status' => 1,
            'attachment_id' => $attachment_id,
            'upload_at' => current_time('mysql')
        ],
        ["{$prefix}_id" => $entity_id, 'id' => $file_id]
    );
}

function create_attachment($upload_data) {
    $attachment = [
        'post_mime_type' => $upload_data['type'],
        'post_title' => sanitize_file_name($upload_data['file']),
        'post_content' => '',
        'post_status' => 'inherit'
    ];

    $attach_id = wp_insert_attachment($attachment, $upload_data['file']);
    if (!$attach_id) return false;

    $attach_data = wp_generate_attachment_metadata($attach_id, $upload_data['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);
    
    return $attach_id;
}

function handle_post_processing($wpdb, $current_user, $tables, $student_id) {
    WC()->mailer()->get_emails()['WC_Update_Document_Email']->trigger($student_id);

    $required_docs = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$tables['documents']} WHERE is_required = 1 AND student_id = %d",
        $student_id
    ));

    $access_virtual = check_virtual_access($required_docs);
    if ($access_virtual && check_payment_status($wpdb, $tables['payments'], $student_id)) {
        handle_virtual_classroom($wpdb, $tables['students'], $student_id, $current_user);
    }
}

function check_virtual_access($documents) {
    foreach ($documents as $doc) {
        if ($doc->status != 5) return false;
    }
    return true;
}

function check_payment_status($wpdb, $table, $student_id) {
    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$table} WHERE student_id = %d AND product_id = %d",
        $student_id, 
        AES_FEE_INSCRIPTION
    ));
}

function handle_virtual_classroom($wpdb, $table, $student_id, $current_user) {
    $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $student_id));
    
    $user_data = prepare_user_data($student, $current_user);
    $files_to_send = prepare_files_data($wpdb, $student_id);

    create_user_laravel(array_merge($user_data, ['files' => $files_to_send]));
    update_status_student($student_id, 2);

    if (in_array('parent', $current_user->roles) && !in_array('student', $current_user->roles)) {
        create_user_student($student_id);
    }

    handle_moodle_integration($wpdb, $table, $student_id);
}

function prepare_user_data($student, $current_user) {
    $type_document_map = [
        'identification_document' => 1,
        'passport' => 2,
        'ssn' => 4
    ];

    $gender_map = [
        'male' => 'M',
        'female' => 'F'
    ];

    $grade_map = [1 => 9, 2 => 10, 3 => 11, 4 => 12];

    return [
        'id_document' => $student->id_document,
        'type_document' => $type_document_map[$student->type_document] ?? 1,
        'firstname' => "{$student->name} {$student->middle_name}",
        'lastname' => "{$student->last_name} {$student->middle_last_name}",
        'birth_date' => $student->birth_date,
        'phone' => $student->phone,
        'email' => $student->email,
        'etnia' => $student->ethnicity,
        'grade' => $grade_map[$student->grade_id] ?? 9,
        'gender' => $gender_map[$student->gender] ?? 'M',
        'cod_period' => $student->academic_period,
        'cod_program' => AES_PROGRAM_ID,
        'cod_tip' => AES_TYPE_PROGRAM,
        'address' => get_user_meta($student->partner_id, 'billing_address_1', true),
        'country' => get_user_meta($student->partner_id, 'billing_country', true),
        'city' => get_user_meta($student->partner_id, 'billing_city', true),
        'postal_code' => get_user_meta($student->partner_id, 'billing_postcode', true) ?: '-',
    ];
}

function prepare_files_data($wpdb, $student_id) {
    $documents = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}student_documents WHERE student_id = %d",
        $student_id
    ));

    $files = [];
    foreach ($documents as $doc) {
        if (!$doc->attachment_id) continue;
        
        $id_requisito = $wpdb->get_var($wpdb->prepare(
            "SELECT id_requisito FROM {$wpdb->prefix}documents WHERE name = %s",
            $doc->document_id
        ));

        $file_path = get_attached_file($doc->attachment_id);
        if ($file_path) {
            $files[] = [
                'file' => curl_file_create($file_path, mime_content_type($file_path)), 
                'id_requisito' => $id_requisito
            ];
        }
    }
    return $files;
}

function handle_moodle_integration($wpdb, $table, $student_id) {
    $moodle_user = is_search_student_by_email($student_id);
    
    if (!$moodle_user) {
        create_user_moodle($student_id);
    } else {
        $password = ensure_moodle_password($wpdb, $table, $student_id, $moodle_user);
        $wpdb->update($table, [
            'moodle_student_id' => $moodle_user[0]['id'],
            'moodle_password' => $password
        ], ['id' => $student_id]);
    }
}

function ensure_moodle_password($wpdb, $table, $student_id, $moodle_user) {
    $password = $wpdb->get_var($wpdb->prepare(
        "SELECT moodle_password FROM {$table} WHERE id = %d",
        $student_id
    ));

    if (!$password) {
        $password = generate_password_user();
        change_password_user_moodle($student_id);
    }
    
    return $password;
}

function handle_missing_documents_redirect($wpdb, $current_user) {
    if (!isset($_GET['missing'])) return;

    $missing = json_decode($_GET['missing']);
    foreach ($missing as $student_id) {
        $student = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}students WHERE id = %d", 
            $student_id
        ));

        $user_student = get_user_by('email', $student->email);
        $signature_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}users_signatures 
             WHERE user_id = %d AND document_id = 'MISSING DOCUMENTS'",
            $user_student->ID
        ));

        $document_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}student_documents 
             WHERE student_id = %d AND document_id = 'MISSING DOCUMENTS'",
            $student->id
        ));

        if ($signature_exists || !$document_exists) {
            wp_redirect(wc_get_endpoint_url(
                'student-documents', 
                '', 
                get_permalink(get_option('woocommerce_myaccount_page_id'))
            ));
            exit;
        }
    }
}

add_action('wp_loaded','save_document');


add_action('wp_ajax_nopriv_save_documents', 'save_documents');
add_action('wp_ajax_save_documents', 'save_documents');
function save_documents()
{
    global $wpdb,$current_user;
    $roles = $current_user->roles;
    $table_student_documents = $wpdb->prefix.'student_documents';
    $table_students = $wpdb->prefix.'students';
    $table_users_signatures = $wpdb->prefix.'users_signatures';
    // $missing_documents = [];
    // $user_signature = null;
    // $pending_required_documents = false;
    if(isset($_POST['students']) && !empty($_POST['students'])){

        $students = $_POST['students'];

        /* foreach student */
        foreach($students as $student_id){
            $files = $_POST['file_student_'.$student_id.'_id'];

            foreach($files as $file_id){
                
                $status = $_POST['status_file_'.$file_id.'_student_id_'.$student_id];

                if(isset($_FILES['document_'.$file_id.'_student_id_'.$student_id]) && !empty($_FILES['document_'.$file_id.'_student_id_'.$student_id])){
                    $file_temp = $_FILES['document_'.$file_id.'_student_id_'.$student_id];
                }else{
                    $file_temp = [];
                }

                if($status == 0 || $status == 3 || $status == 4){

                    if(!empty($file_temp['tmp_name'])){
                        
                        $upload_data = wp_handle_upload($file_temp,array('test_form' => FALSE) );
                    
                        if ($upload_data && !is_wp_error($upload_data)) {
                            
                            $attachment = array(
                                'post_mime_type' => $upload_data['type'],
                                'post_title' => $file_id,
                                'post_content' => '',
                                'post_status' => 'inherit'
                            );
                            
                            $attach_id = wp_insert_attachment($attachment, $upload_data['file']);
                            $deleted = wp_delete_attachment($upload_data['file'], true );
                            $attach_data = wp_generate_attachment_metadata($attach_id, $upload_data['file']);
                            wp_update_attachment_metadata($attach_id, $attach_data);
                            $wpdb->update($table_student_documents,['status' => 1,'attachment_id' => $attach_id, 'upload_at' => date('Y-m-d H:i:s')],['student_id' => $student_id,'id' => $file_id ]);
                        }
                    } else {
                        $file_is_required = $_POST['file_is_required'.$file_id.'_student_id_'.$student_id];
                        // if ($file_is_required == 1 && !$pending_required_documents) {
                        //     $pending_required_documents = true;
                        // }

                        // if (!in_array($student_id, $missing_documents)) {
                        //     array_push($missing_documents, $student_id);
                        // }

                    }
                }
            }

            // if (sizeof($missing_documents) > 0) {
            //     $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$student_id}");
            //     $user_student = get_user_by('email', $student->email);
            //     $user_signature = $wpdb->get_row("SELECT * FROM {$table_users_signatures} WHERE user_id = {$user_student->ID} AND document_id='MISSING DOCUMENTS'");
            // } else {
                $email_update_document = WC()->mailer()->get_emails()['WC_Update_Document_Email'];
                $email_update_document->trigger($student_id);

                $access_virtual = true;

                $documents_student = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE is_required = 1 AND student_id={$student_id}");

                if($documents_student){
                    foreach($documents_student as $document){
                        if($document->status != 5){
                            $access_virtual = false;
                        }
                    }

                    // VER  IFICAR FEE DE INSCRIPCION
                    global $wpdb;
                    $table_student_payment = $wpdb->prefix.'student_payments';
                    $table_students = $wpdb->prefix.'students';
                    $partner_id = get_current_user_id();
                    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE partner_id = {$partner_id}");
                    $student_id = $student->id;
                    $paid = $wpdb->get_row("SELECT * FROM {$table_student_payment} WHERE student_id={$student_id} and product_id = ". AES_FEE_INSCRIPTION);
                    // VERIFICAR FEE DE INSCRIPCION

                    //virtual classroom
                    if($access_virtual && isset($paid)){
                        $table_name = $wpdb->prefix . 'students'; // assuming the table name is "wp_students"
                        $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $student_id));
                        $type_document = array(
                            'identification_document' => 1,
                            'passport' => 2,
                            'ssn' => 4,
                        )[$student->type_document];
        
                        $files_to_send = array();

                        $type_document = '';
                        switch ($student->type_document) {
                            case 'identification_document':
                                $type_document = 1;
                                break;
                            case 'passport':
                                $type_document = 2;
                                break;
                            case 'ssn':
                                $type_document = 4;
                                break;
                        }
        
                        $type_document_re = '';
                        if (get_user_meta($student->partner_id, 'type_document', true)) {
                            switch (get_user_meta($student->partner_id, 'type_document', true)) {
                                case 'identification_document':
                                    $type_document_re = 1;
                                    break;
                                case 'passport':
                                    $type_document_re = 2;
                                    break;
                                case 'ssn':
                                    $type_document_re = 4;
                                    break;
                            }
                        } else {
                            $type_document_re = 1;
                        }
        
        
                        $gender = '';
                        switch ($student->gender) {
                            case 'male':
                                $gender = 'M';
                                break;
                            case 'female':
                                $gender = 'F';
                                break;
                        }
        
        
                        $gender_re = '';
                        if (get_user_meta($student->partner_id, 'gender', true)) {
                            switch (get_user_meta($student->partner_id, 'gender', true)) {
                                case 'male':
                                    $gender_re = 'M';
                                    break;
                                case 'female':
                                    $gender_re = 'F';
                                    break;
                            }
                        } else {
                            $gender_re = 'M';
                        }
        
                        $grade = '';
                        switch ($student->grade_id) {
                            case 1:
                                $grade = 9;
                                break;
                            case 2:
                                $grade = 10;
                                break;
                            case 3:
                                $grade = 11;
                                break;
                            case 4:
                                $grade = 12;
                                break;
                        }
                        $user_partner = get_user_by('id', $student->partner_id);
                        $fields_to_send = array(
                            // DATOS DEL ESTUDIANTE
                            'id_document' => $student->id_document,
                            'type_document' => $type_document,
                            'firstname' => $student->name . ' ' . $student->middle_name,
                            'lastname' => $student->last_name . ' ' . $student->middle_last_name,
                            'birth_date' => $student->birth_date,
                            'phone' => $student->phone,
                            'email' => $student->email,
                            'etnia' => $student->ethnicity,
                            'grade' => $grade,
                            'gender' => $gender,
                            'cod_period' => $student->academic_period,
        
                            // PADRE
                            'id_document_re' => get_user_meta($student->partner_id, 'id_document', true) ? get_user_meta($student->partner_id, 'id_document', true) : '000000',
                            'type_document_re' => $type_document_re,
                            'firstname_re' => get_user_meta($student->partner_id, 'first_name', true),
                            'lastname_re' => get_user_meta($student->partner_id, 'last_name', true),
                            'birth_date_re' => get_user_meta($student->partner_id, 'birth_date', true),
                            'phone_re' => get_user_meta($student->partner_id, 'billing_phone', true),
                            'email_re' => $user_partner->user_email,
                            'gender_re' => $gender_re,
        
                            'cod_program' => AES_PROGRAM_ID,
                            'cod_tip' => AES_TYPE_PROGRAM,
                            'address' => get_user_meta($student->partner_id, 'billing_address_1', true),
                            'country' => get_user_meta($student->partner_id, 'billing_country', true),
                            'city' => get_user_meta($student->partner_id, 'billing_city', true),
                            'postal_code' => get_user_meta($student->partner_id, 'billing_postcode', true) ? get_user_meta($student->partner_id, 'billing_postcode', true) : '-',
                        );
        
                        $all_documents_student = $wpdb->get_results("SELECT * FROM {$table_student_documents} WHERE student_id={$student_id}");
                        $documents_to_send = [];
                        foreach ($all_documents_student as $document) {
                            if ($document->attachment_id) {
                                array_push($documents_to_send, $document);
                            }
                        }
        
                        foreach ($documents_to_send as $key => $doc) {
                            $id_requisito = $wpdb->get_var($wpdb->prepare("SELECT id_requisito FROM {$wpdb->prefix}documents WHERE name = %s", $doc->document_id));
                            $attachment_id = $doc->attachment_id;
                            $attachment_path = get_attached_file($attachment_id);
                            if ($attachment_path) {
                                $file_name = basename($attachment_path);
                                $file_type = mime_content_type($attachment_path);
        
                                $files_to_send[] = array(
                                    'file' => curl_file_create($attachment_path, $file_type, $file_name),
                                    'id_requisito' => $id_requisito
                                );
                            }
                        }
        
                        create_user_laravel(array_merge($fields_to_send, array('files' => $files_to_send)));
        
                        update_status_student($student_id, 2);

                        if(in_array('parent',$roles) && !in_array('student',$roles)){
                            create_user_student($student_id);
                        }

                        $exist = is_search_student_by_email($student_id);
                    
                        if(!$exist){
                            create_user_moodle($student_id);
                        }else{
                            $wpdb->update($table_students,['moodle_student_id' => $exist[0]['id']],['id' => $student_id]);

                            $is_exist_password = is_password_user_moodle($student_id);

                            if(!$is_exist_password){
                                
                                $password = generate_password_user();
                                $wpdb->update($table_students,['moodle_password' => $password],['id' => $student_id]);
                                change_password_user_moodle($student_id);
                            }
                        }
                    }
                }
            // }

        }

        
    }

    wc_add_notice( __( 'Documents saved successfully.', 'edusystem' ), 'success' );
    wp_send_json(array('success' => true));
    exit;
}

function view_pending_documents(){
    
    global $current_user;
    $roles = $current_user->roles;

    $student_status = get_user_meta($current_user->ID,'status_register',true);

    if(!in_array('parent',$roles) && in_array('student',$roles)){
        $student_id = get_user_meta(get_current_user_id(),'student_id',true);
        if($student_id){
            $students = get_student_from_id($student_id);
        }else{
            $students = get_student(get_current_user_id());
        }
    }

    if (in_array('parent',$roles) && in_array('student',$roles) || in_array('parent',$roles) && !in_array('student',$roles)) {
        $students = get_student(get_current_user_id());
    }

    $solvency_administrative = true;

    if(in_array('parent',$roles) && in_array('student',$roles)){

        if($student_status == 1 || $student_status == '1'){

            foreach($students as $student){
                $documents = get_documents($student->id);

                foreach($documents as $document){

                    if($document->status != 5){
                        $solvency_administrative = false;
                    }
                }
            }
        
            if(!$solvency_administrative){
                include(plugin_dir_path(__FILE__).'templates/pending-documents.php');
            }
        }

    }else if(in_array('parent',$roles) && !in_array('student',$roles)){

        if($student_status == 1 || $student_status == '1'){

            foreach($students as $student){
                $documents = get_documents($student->id);

                foreach($documents as $document){

                    if($document->status != 5){
                        $solvency_administrative = false;
                    }
                }
            }
        
            if(!$solvency_administrative){
                include(plugin_dir_path(__FILE__).'templates/pending-documents.php');
            }
        }

    }else if(!in_array('parent',$roles) && in_array('student',$roles)){
        include(plugin_dir_path(__FILE__).'templates/pending-documents.php');
    }

}

add_action('woocommerce_account_dashboard','view_pending_documents');

function get_name_document($document_id){
    /*
    $name = match ($document_id) {
        'certified_notes_high_school' => __('CERTIFIED NOTES HIGH SCHOOL','edusystem'),
        'high_school_diploma' => __('HIGH SCHOOL DIPLOMA','edusystem'),
        'id_parents' => __('ID OR CI OF THE PARENTS','edusystem'),
        'id_student' => __('ID STUDENTS','edusystem'),
        'photo_student_card' => __('STUDENT'S PHOTO','edusystem'),
        'proof_of_grades' => __('PROOF OF GRADE','edusystem'),
        'proof_of_study' => __('PROOF OF STUDY','edusystem'),
        'vaccunation_card' => __('VACCINATION CARD','edusystem'),
        default => '',
    };

    return $name;
    */
    return $document_id;
}

function get_help_info_document($document_id){
    $text = '';

    if ($document_id == 'CERTIFIED NOTES HIGH SCHOOL') {
        $text = 'Provide an official transcript or report card certified by a school authority. The allowed file type is ' . get_type_file_document($document_id);
    } else if ($document_id == 'HIGH SCHOOL DIPLOMA') {
        $text = 'Ensure you provide an official copy issued by the school of your high school diploma. The allowed file type is ' . get_type_file_document($document_id);
    } else if ($document_id == 'ID OR CI OF THE PARENTS') {
        $text = 'Please provide a clear and legible copy of the document. The allowed file type is ' . get_type_file_document($document_id);
    } else if ($document_id == 'ID STUDENTS') {
        $text = 'Please ensure you provide a clear and legible copy of the identification document. The allowed file type is ' . get_type_file_document($document_id);
    } else if ($document_id == 'STUDENT\'S PHOTO') {
        $text = "<div>Please provide a recent, clear, and high-quality photo of the student</div> <div><img src='https://img.freepik.com/vector-gratis/cara-hombre-estilo-plano_90220-2877.jpg' style='width: 100px; margin: auto; padding: 10px' /></div> <div>The allowed file type is " . get_type_file_document($document_id) . "</div>";
    } else if ($document_id == 'PROOF OF GRADE') {
        $text = 'Please provide an official document that clearly indicates the student\'s name, the course or subject, and the corresponding grade achieved. The allowed file type is ' . get_type_file_document($document_id);
    } else if ($document_id == 'PROOF OF STUDY') {
        $text = 'Please provide an official document that verifies the student\'s enrollment status. The allowed file type is ' . get_type_file_document($document_id);
    } else if ($document_id == 'VACCINATION CARD') {
        $text = 'The card should clearly display the student\'s name, the type of vaccine received, the dates of vaccination, and any booster shots administered. The allowed file type is ' . get_type_file_document($document_id);
    } else if ($document_id == 'PHOTO') {
        $text = 'teacher\'s profile picture, this will be visible to the public. The allowed file type is ' . get_type_file_document_teacher($document_id);
    } else if ($document_id == 'FORM 402') {
        $text = 'FORM 402 is a document used in the U.S. immigration process, specifically related to the application for citizenship or naturalization. The allowed file type is ' . get_type_file_document_teacher($document_id);
    } else if ($document_id == 'DIGITAL COPY OF UNDERGRADUATE DEGREE') {
        $text = 'Please provide a DIGITAL COPY OF YOUR UNIVERSITY DEGREE to validate your information and continue with the registration process. The allowed file type is ' . get_type_file_document_teacher($document_id);
    } else if ($document_id == 'DIGITAL COPY OF THE GRADUATE DEGREE') {
        $text = 'Please provide DIGITAL COPY OF POSTGRADUATE DEGREE to validate your information and continue with the registration. The allowed file type is ' . get_type_file_document_teacher($document_id);
    } else if ($document_id == 'CURRICULAR SUMMARY') {
        $text = 'Please provide your CURRICULAR SUMMARY to validate your information and continue with the registration process. The allowed file type is ' . get_type_file_document_teacher($document_id);
    }

    return $text;
}

function get_type_file_document($document_id) {
    global $wpdb;
    $table_documents = $wpdb->prefix . 'documents';
    
    // Usar prepare para evitar problemas con apóstrofes y inyección SQL
    $query = $wpdb->prepare("SELECT * FROM {$table_documents} WHERE name = %s", $document_id);
    $doc = $wpdb->get_row($query);
    
    return $doc ? $doc->type_file : null; // Devuelve null si no se encuentra el documento
}

function get_type_file_document_teacher($document_id) {
    global $wpdb;
    $table_documents_for_teachers = $wpdb->prefix . 'documents_for_teachers';
    
    // Usar prepare para evitar problemas con apóstrofes y inyección SQL
    $query = $wpdb->prepare("SELECT * FROM {$table_documents_for_teachers} WHERE name = %s", $document_id);
    $doc = $wpdb->get_row($query);
    
    return $doc ? $doc->type_file : null; // Devuelve null si no se encuentra el documento
}

function get_status_document($status_id){

    $status = match ($status_id){
        '0' => __('No sent','edusystem'),
        '1' => __('Sent','edusystem'),
        '2' => __('Processing','edusystem'),
        '3' => __('Declined','edusystem'),
        '4' => __('Expired','edusystem'),
        '5' => __('Approved','edusystem'),
        default => '',
    };

    return $status;
}

function get_name_type_document($type_document){

    $type_document_parent = match($type_document){
        'passport' => __('Passport','edusystem'),
        'identification_document' => __('Identification Document','edusystem'),
        'ssn' => __('SSN'),
        default => '',
    };

    return $type_document_parent;
}
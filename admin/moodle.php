<?php

function show_moodle_setting()
{

    if (isset($_GET['action']) && !empty($_GET['action'])) {
        if ($_GET['action'] == 'save_setting') {

            if (isset($_POST['moodle_url']) && !empty($_POST['moodle_url'])) {
                update_option('moodle_url', $_POST['moodle_url']);
            } else {
                update_option('moodle_url', '');
            }

            if (isset($_POST['moodle_token']) && !empty($_POST['moodle_token'])) {
                update_option('moodle_token', $_POST['moodle_token']);
            } else {
                update_option('moodle_token', '');
            }
        }

        wp_redirect(admin_url('admin.php?page=moodle-setting'));
        exit;

    } else {
        include(plugin_dir_path(__FILE__) . 'templates/moodle-setting.php');
    }
}

function create_user_moodle($student_id)
{
    try {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';

        $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
        $address = get_user_meta($data_student->partner_id, 'billing_address_1', true);

        $moodle_url = get_option('moodle_url');
        $moodle_token = get_option('moodle_token');

        if (!empty($moodle_url) && !empty($moodle_token)) {

            $MoodleRest = new MoodleRest($moodle_url . 'webservice/rest/server.php', $moodle_token);
            $password = wp_generate_password(12);

            $users = [
                'users' => [
                    [
                        'username' => strtolower((string) $data_student->id_document),
                        'firstname' => $data_student->name,
                        'lastname' => $data_student->last_name,
                        'email' => $data_student->email,
                        'password' => $password,
                        'city' => $data_student->city,
                        'country' => $data_student->country,
                        'middlename' => $data_student->middle_name,
                        'idnumber' => $data_student->id_document,
                        'phone2' => $data_student->phone,
                        'institution' => $data_student->name_institute,
                        'address' => $address,
                        'lang' => 'en'
                    ]
                ]
            ];

            $create_user = $MoodleRest->request('core_user_create_users', $users, MoodleRest::METHOD_POST);
            $wpdb->update($table_students, [
                'moodle_student_id' => $create_user[0]['id'],
                'moodle_password' => $password
            ], ['id' => $student_id]);

            generate_projection_student($student_id);

            if (get_option('auto_enroll_regular')) {
                automatically_enrollment($student_id);
            }

            if (get_option('public_course_id')) {
                enroll_student_public_course(courses_enroll_student($student_id, [(int) get_option('public_course_id')]));
            }

            return $create_user;
        }
    } catch (\Throwable $th) {
        return;
    }
}

/**
 * status: 
 * 1) nologin - can not access
 * 2) manual - can access
 *  
 */

function change_status_student($student_id, $status = 'manual')
{
    try {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';

        $moodle_url = get_option('moodle_url');
        $moodle_token = get_option('moodle_token');

        $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

        if (!empty($moodle_url) && !empty($moodle_token)) {

            if (!$data_student) {

                $moodle_student_id = $data_student->moodle_student_id;

                if (!empty($moodle_student_id)) {

                    $MoodleRest = new MoodleRest($moodle_url . 'webservice/rest/server.php', $moodle_token);

                    $users = [
                        'users' => [
                            [
                                'id' => $moodle_student_id,
                                'auth' => $status,
                            ]
                        ]
                    ];

                    $update_user = $MoodleRest->request('core_user_update_users', $users, MoodleRest::METHOD_POST);
                    return $update_user;
                }

            }
        }
    } catch (\Throwable $th) {
        return;
    }

}

function is_search_student_by_email($student_id)
{
    try {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';

        $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

        $moodle_url = get_option('moodle_url');
        $moodle_token = get_option('moodle_token');

        if (!empty($data_student)) {

            if (!empty($moodle_url) && !empty($moodle_token)) {

                $MoodleRest = new MoodleRest($moodle_url . 'webservice/rest/server.php', $moodle_token);

                $search = [
                    'field' => 'email',
                    'values' => [
                        $data_student->email
                    ]
                ];

                $search_user = $MoodleRest->request('core_user_get_users_by_field', $search);

                if (empty($search_user)) {
                    return [];
                } else {
                    return $search_user;
                }
            }
        }
    } catch (\Throwable $th) {
        return [];
    }
}

function get_courses_moodle()
{
    try {
        $courses = [];
        $moodle_url = get_option('moodle_url');
        $moodle_token = get_option('moodle_token');

        if (!empty($moodle_url) && !empty($moodle_token)) {
            $MoodleRest = new MoodleRest($moodle_url . 'webservice/rest/server.php', $moodle_token);
            $courses = $MoodleRest->request('core_course_get_courses');

            return $courses;
        }
    } catch (\Throwable $th) {
        return [];
    }
}

function change_password_user_moodle($student_id)
{
    try {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';

        $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

        $moodle_url = get_option('moodle_url');
        $moodle_token = get_option('moodle_token');

        if (!empty($moodle_url) && !empty($moodle_token)) {

            if ($data_student) {

                $moodle_student_id = $data_student->moodle_student_id;

                if (!empty($moodle_student_id)) {

                    $MoodleRest = new MoodleRest($moodle_url . 'webservice/rest/server.php', $moodle_token);

                    $users = [
                        'users' => [
                            [
                                'id' => $moodle_student_id,
                                'password' => $data_student->moodle_password,
                            ]
                        ]
                    ];

                    $update_user = $MoodleRest->request('core_user_update_users', $users, MoodleRest::METHOD_POST);
                    return $update_user;
                }
            }
        }
    } catch (\Throwable $th) {
        return false;
    }
}

function get_url_login($email)
{
    try {
        $moodle_url = get_option('moodle_url');
        $moodle_token = get_option('moodle_token');

        if (!empty($moodle_url) && !empty($moodle_token)) {

            $MoodleRest = new MoodleRest($moodle_url . 'webservice/rest/server.php', $moodle_token);

            $data = ['user' => ['email' => $email]];

            $url = $MoodleRest->request('auth_userkey_request_login_url', $data, MoodleRest::METHOD_POST);

            // Intento de inicio de sesión de Moodle
            $user = get_user_by('email', $email);
            if ($user) {

                $first_name = get_user_meta( $user->ID, 'first_name', true );
                $last_name = get_user_meta( $user->ID, 'last_name', true );

                if (empty($url)) {
                    // Error during login
                    $message = sprintf(__('The student %s encountered an error while logging into Moodle.', 'edusystem'), $first_name.' '.$last_name);
                    $type = 'error_moodle_login';
                } else {
                    // Successful login
                    $message = sprintf(__('The student %s successfully logged into Moodle.', 'edusystem'), $first_name.' '.$last_name);
                    $type = 'moodle_login';
                }
                edusystem_get_log($message, $type, $user->ID);
            }

            return $url['loginurl'];
        }
    } catch (\Throwable $th) {

        $user = get_user_by('email', $email);
        if ($user) {
            
            $first_name = get_user_meta( $user->ID, 'first_name', true );
            $last_name = get_user_meta( $user->ID, 'last_name', true );

            $message = sprintf(__('The student %s encountered an error while logging into Moodle.', 'edusystem'), $first_name.' '.$last_name);
            $type = 'error_moodle_login';
            $user_id = $user->ID;
        } else {
            $message = __('Error trying to access Moodle.', 'edusystem');
            $type = 'error_moodle';
            $user_id = 0;
        }
        edusystem_get_log($message, $type, $user_id);


        return [];
    }
}

function courses_enroll_student($student_id, $courses = [])
{
    try {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
        $enrollments = [];

        if (!empty($data_student) && $data_student->moodle_student_id) {
            foreach ($courses as $key => $course_id) {
                array_push($enrollments, [
                    'userid' => $data_student->moodle_student_id,
                    'courseid' => $course_id,
                    'roleid' => ROLE_ID_STUDENT_MOODLE,
                ]);
            }
        }

        return $enrollments;
    } catch (\Throwable $th) {
        return [];
    }
}

function enroll_student($enrollments = [])
{
    try {
        global $wpdb;

        $moodle_url = get_option('moodle_url');
        $moodle_token = get_option('moodle_token');

        $MoodleRest = new MoodleRest($moodle_url . 'webservice/rest/server.php', $moodle_token);

        // Dividir el array en chunks de 25 elementos
        $chunks = array_chunk($enrollments, 25);
        $all_responses = [];

        foreach ($chunks as $chunk) {
            $response = $MoodleRest->request('enrol_manual_enrol_users', ['enrolments' => $chunk]);
            if (!empty($response)) {
                $all_responses = array_merge($all_responses, $response);
            }
        }

        if (empty($all_responses)) {
            return [];
        } else {
            return $all_responses;
        }
    } catch (\Throwable $th) {
        return [];
    }
}

function enroll_student_public_course($enrollments = [])
{
    try {
        global $wpdb;

        $moodle_url = get_option('moodle_url');
        $moodle_token = get_option('moodle_token');

        $MoodleRest = new MoodleRest($moodle_url . 'webservice/rest/server.php', $moodle_token);

        // Dividir el array en chunks de 25 elementos
        $chunks = array_chunk($enrollments, 25);
        $all_responses = [];

        foreach ($chunks as $chunk) {
            $response = $MoodleRest->request('enrol_manual_enrol_users', ['enrolments' => $chunk]);
            if (!empty($response)) {
                $all_responses = array_merge($all_responses, $response);
            }
        }

        if (empty($all_responses)) {
            return [];
        } else {
            return $all_responses;
        }
    } catch (\Throwable $th) {
        return [];
    }
}

function courses_unenroll_student($student_id, $course_id)
{
    try {
        global $wpdb;
        $table_students = $wpdb->prefix . 'students';
        $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
        $enrollments = [];

        if (!empty($data_student) && $data_student->moodle_student_id) {
            array_push($enrollments, [
                'userid' => $data_student->moodle_student_id,
                'courseid' => $course_id
            ]);
        }

        return $enrollments;
    } catch (\Throwable $th) {
        return [];
    }
}

function unenroll_student($enrollments = [])
{
    try {
        $moodle_url = get_option('moodle_url');
        $moodle_token = get_option('moodle_token');

        $MoodleRest = new MoodleRest($moodle_url . 'webservice/rest/server.php', $moodle_token);
        $unenrolled_courses = $MoodleRest->request('enrol_manual_unenrol_users', ['enrolments' => $enrollments]);
        if (empty($unenrolled_courses)) {
            return [];
        } else {
            return $unenrolled_courses;
        }
    } catch (\Throwable $th) {
        return [];
    }
}

/**
 * Sincroniza un estudiante con Moodle.
 * Busca al usuario por email. Si existe, actualiza el ID local. Si no, lo crea en Moodle.
 *
 * @param int $student_id El ID del estudiante en WordPress.
 * @return array|bool Devuelve los datos del usuario de Moodle (existente o recién creado) o false si ocurre un error.
 */
function sync_student_with_moodle($student_id)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';

    // 1. Obtiene los datos del estudiante de forma segura
    $student_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_students} WHERE id = %d", $student_id));
    if (!$student_data) {
        // No se pudo encontrar al estudiante en la base de datos local
        return false;
    }

    // 2. Obtiene las credenciales de Moodle una sola vez
    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');
    if (empty($moodle_url) || empty($moodle_token)) {
        // Faltan las credenciales de la API de Moodle
        return false;
    }

    // 3. revisamos si completo el formulario (FGU)
    if (has_action('portal_create_user_external')) {
        $user_student = get_user_by('email', $student_data->email);
        $meta_value = get_user_meta($user_student->ID, 'complete_data_success', true);
        if (!$meta_value) {
            return false;
        }
    }

    try {
        $MoodleRest = new MoodleRest($moodle_url . 'webservice/rest/server.php', $moodle_token);

        // 3. Busca al usuario en Moodle por su email
        $search_params = [
            'field' => 'email',
            'values' => [$student_data->email]
        ];
        $existing_user_search = $MoodleRest->request('core_user_get_users_by_field', $search_params);
        
        // 4. LÓGICA CLAVE: Decide si actualizar o crear
        if (!empty($existing_user_search) && isset($existing_user_search[0]['id'])) {
            // -- EL USUARIO YA EXISTE --
            $moodle_user = $existing_user_search[0];
            
            // Actualiza la tabla local solo con el ID de Moodle encontrado
            $wpdb->update(
                $table_students,
                ['moodle_student_id' => $moodle_user['id']], // El dato a actualizar
                ['id' => $student_id]                       // Dónde actualizarlo
            );
            
            return $moodle_user; // Devuelve los datos del usuario encontrado

        } else {
            // -- EL USUARIO NO EXISTE --
            // Llama a la función auxiliar para crear el usuario
            return create_user_moodle($student_id);
        }

    } catch (\Throwable $th) {
        // Manejo de errores de la API
        return false;
    }
}
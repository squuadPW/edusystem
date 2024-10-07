<?php 
function is_enrolled_in_courses($student_id) {
    global $wpdb;
    $table_students = $wpdb->prefix.'students';

    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");

    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    if (!empty($data_student)) {
        if (!empty($moodle_url) && !empty($moodle_token)) {
            $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php', $moodle_token);

            $enrolments = [
                'userid' => $data_student->moodle_student_id, // asumo que el campo moodle_id existe en la tabla students
            ];

            $enrolled_courses = $MoodleRest->request('core_enrol_get_users_courses', $enrolments);

            if (empty($enrolled_courses)) {
                return [];
            } else {
                return $enrolled_courses;
            }
        }
    }
}
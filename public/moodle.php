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

function student_unsubscribe_moodle($student_id) {
    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
    $courses = is_enrolled_in_courses($student_id);

    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    if (!empty($data_student)) {
        if (!empty($moodle_url) && !empty($moodle_token)) {
            $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php', $moodle_token);
            $enrolments = []; // Inicializa el array vacío
            $courses_delete = [];
            foreach ($courses as $key => $course) {
                $enrolments[] = [ // Agrega un nuevo sub-array a $enrolments
                    'userid' => (int)$data_student->moodle_student_id,
                    'courseid' => (int)$course['id'],
                ];

                array_push($courses_delete, ['shortname' => $course['shortname']]);
            }

            $MoodleRest->request('enrol_manual_unenrol_users', ['enrolments' => $enrolments ]);
            return $courses_delete;
        }
    }
}

function student_assignments_moodle($student_id) {
    global $wpdb;
    $table_students = $wpdb->prefix.'students';
    $data_student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id={$student_id}");
    $courses = is_enrolled_in_courses($student_id);

    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    if (!empty($data_student)) {
        if (!empty($moodle_url) && !empty($moodle_token)) {
            $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php', $moodle_token);
            $courseids = [];
            $grades = [];
            $moodle_student_id = $data_student->moodle_student_id;

            foreach ($courses as $key => $course) {
                if ($course['visible']) {
                    array_push($courseids, (int)$course['id']);

                    $grades_course = course_grade((int)$course['id']);
                    $grades_course = $grades_course['usergrades'];
                    $filtered_grades = array_filter($grades_course, function($entry) use ($moodle_student_id) {
                        return $entry['userid'] == $moodle_student_id;
                    });
                    $filtered_grades = array_values($filtered_grades);

                    array_push($grades, ['course_id' => (int)$course['id'], 'grades' => $filtered_grades]);
                }
            }

            $assignments = $MoodleRest->request('mod_assign_get_assignments', ['courseids' => $courseids ]);
            return ['assignments' => $assignments['courses'], 'grades' => $grades];
        }
    }
}

function course_grade($course_id) {

    $moodle_url = get_option('moodle_url');
    $moodle_token = get_option('moodle_token');

    if (!empty($moodle_url) && !empty($moodle_token)) {
        $MoodleRest = new MoodleRest($moodle_url.'webservice/rest/server.php', $moodle_token);
        $grades = $MoodleRest->request('gradereport_user_get_grade_items', ['courseid' => $course_id]);
        return $grades;
    }
}
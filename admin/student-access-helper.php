<?php

if (!function_exists('edusystem_get_student_classroom_access')) {
    function edusystem_get_student_classroom_access($student_identifier)
    {
        global $wpdb;

        $table_students = $wpdb->prefix . 'students';
        $student = null;

        // Accept ID, array or object
        if (is_numeric($student_identifier)) {
            $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_students} WHERE id = %d LIMIT 1", $student_identifier));
        } elseif (is_array($student_identifier) || is_object($student_identifier)) {
            $student = is_object($student_identifier) ? $student_identifier : (object) $student_identifier;
        }

        $result = [
            'has_moodle' => false,
            'access' => true,
            'error' => '',
            'status_text' => '',
            'background_color' => '#dfdedd',
        ];

        if (!$student) {
            $result['access'] = false;
            $result['error'] = __('Student data not found. Please contact support.', 'edusystem');
            $result['has_moodle'] = false;
        } else {
            $result['has_moodle'] = !empty($student->moodle_student_id);

            if (empty($student->moodle_student_id)) {
                $result['access'] = false;
                $result['error'] = __('No Moodle student ID assigned.', 'edusystem');
            }

            if (function_exists('are_required_documents_approved_deadline') && are_required_documents_approved_deadline($student->id) === false) {
                $result['access'] = false;
                $result['error'] = __('You are missing some of the documents required for access.', 'edusystem');
            }

            $today = date('Y-m-d');
            if (!empty($student->max_access_date) && $student->max_access_date < $today) {
                $result['access'] = false;
                $result['error'] = __('The student has lost access to the classroom due to late payments.', 'edusystem');
            }

            if (function_exists('expired_documents') && expired_documents($student->id)) {
                $result['access'] = false;
                $result['error'] = 'The deadline for uploading some documents has expired, removing your access to the virtual classroom. We invite you to access your documents area for more information.';
            }
        }

        // Build status text & color
        if (!$result['has_moodle']) {
            $result['status_text'] = __('Without classroom', 'edusystem');
            $result['background_color'] = '#dfdedd';
        } else {
            $result['status_text'] = $result['access'] ? __('Full access to classroom', 'edusystem') : __('Classroom access removed', 'edusystem');
            $result['background_color'] = $result['access'] ? '#f98012' : '#f980127d';
        }

        return $result;
    }
}

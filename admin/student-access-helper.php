<?php

if (!function_exists('edusystem_get_student_classroom_access')) {
    function edusystem_get_student_classroom_access($student_identifier)
    {
        global $wpdb;

        $table_students = $wpdb->prefix . 'students';
        $student = null;

        if (is_numeric($student_identifier)) {
            $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_students} WHERE id = %d LIMIT 1", $student_identifier));
        } elseif (is_array($student_identifier) || is_object($student_identifier)) {
            $student = is_object($student_identifier) ? $student_identifier : (object) $student_identifier;
        }

        $result = [
            'has_moodle'       => false,
            'access'           => true,
            'error'            => '',
            'status_text'      => '',
            'background_color' => '#dfdedd',
            'student_data'     => $student
        ];

        if (!$student) {
            $result['access'] = false;
            $result['error'] = __('Student data not found. Please contact support.', 'edusystem');
        } else {
            $result['has_moodle'] = !empty($student->moodle_student_id);

            if (!$result['has_moodle']) {
                $result['access'] = false;
                $result['error'] = __('No Moodle student ID assigned.', 'edusystem');
            }

            if ($result['access'] && function_exists('are_required_documents_approved_deadline') && are_required_documents_approved_deadline($student->id) === false) {
                $result['access'] = false;
                $result['error'] = __('Missing required documents. Check the documents section for details.', 'edusystem');
            }

            $today = date('Y-m-d');
            if ($result['access'] && !empty($student->max_access_date) && $student->max_access_date < $today) {
                $result['access'] = false;
                $result['error'] = __('Access removed due to overdue payments. Pay fees to restore access.', 'edusystem');
            }

            if ($result['access'] && function_exists('expired_documents') && expired_documents($student->id)) {
                $result['access'] = false;
                $result['error'] = __('Document deadline expired. Access revoked. Check the documents section for details.', 'edusystem');
            }
        }

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

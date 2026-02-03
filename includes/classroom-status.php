<?php
if (!function_exists('get_classroom_status')) {
    function get_classroom_status($record = null)
    {
        global $wpdb, $current_user;

        // Initialize return structure with sensible defaults
        $result = [
            'text' => __('Without classroom', 'edusystem'),
            'hasMoodleAccess' => false,
            'background' => '#dfdedd',
            'short_text' => __('No classroom', 'edusystem'),
            'badge_class' => 'aes-badge--muted',
            'student_access' => false,
            'error_access' => '',
            'access' => [],
            'subjects_coursing' => [],
            'admin_virtual_access' => get_option('virtual_access'),
            'student' => null,
        ];

        // Resolve $student object from different possible inputs
        $student = null;
        if (is_numeric($record)) {
            $table_students = $wpdb->prefix . 'students';
            $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_students} WHERE id = %d LIMIT 1", $record));
        } elseif (is_array($record)) {
            // convert array to object for consistency
            $student = (object) $record;
        } elseif (is_object($record)) {
            $student = $record;
        } else {
            // Try to detect student from current user
            if (isset($current_user) && $current_user instanceof WP_User) {
                $table_students = $wpdb->prefix . 'students';
                $student_id_meta = get_user_meta($current_user->ID, 'student_id', true);
                $query = $wpdb->prepare("SELECT * FROM {$table_students} WHERE id = %d OR partner_id = %d LIMIT 1", $student_id_meta, $current_user->ID);
                $student = $wpdb->get_row($query);
            }
        }

        if (!$student) {
            // no student => return defaults
            return $result;
        }

        $result['student'] = $student;
        $result['student_access'] = true; // default, will be revoked by checks below

        // Has Moodle id?
        $hasMoodleAccess = !empty($student->moodle_student_id);
        $result['hasMoodleAccess'] = $hasMoodleAccess;

        // Default status text based on moodle id and status_id
        $status_id = isset($student->status_id) ? (int) $student->status_id : null;
        if ($hasMoodleAccess) {
            $result['text'] = ($status_id < 2) ? __('Classroom access removed', 'edusystem') : __('Full access to classroom', 'edusystem');
            $result['background'] = ($status_id < 2) ? '#f980127d' : '#f98012';
        } else {
            $result['text'] = __('Without classroom', 'edusystem');
            $result['background'] = '#dfdedd';
        }

        // Apply the same guard checks as view_access_classroom
        if (empty($student->moodle_student_id)) {
            $result['student_access'] = false;
        } elseif (are_required_documents_approved_deadline($student->id) === false) {
            $result['student_access'] = false;
            $result['error_access'] = __('You are missing some of the documents required for access. Please refer to the documents section for more information.', 'edusystem');
        }

        $today = date('Y-m-d');
        if (!empty($student->max_access_date) && $student->max_access_date < $today) {
            $result['student_access'] = false;
            $result['error_access'] = __('Classroom access has been removed because you have overdue payments. Please pay the outstanding fees in order to continue to have access to the classroom.', 'edusystem');
        }

        if (expired_documents($student->id)) {
            $result['student_access'] = false;
            $result['error_access'] = __('The deadline for uploading some documents has expired, removing your access to the virtual classroom. We invite you to access your documents area for more information.', 'edusystem');
        }

        // Enrollment access (used to determine button state in template)
        $result['access'] = is_enrolled_in_courses($student->id);

        // If student_access is revoked for any reason, override visible text to 'Classroom access removed'
        if ($result['student_access'] === false && $hasMoodleAccess) {
            $result['text'] = __('Classroom access removed', 'edusystem');
            $result['background'] = '#f980127d';
        } elseif ($result['student_access'] === false && !$hasMoodleAccess) {
            // keep 'Without classroom' text if no moodle id
            $result['text'] = __('Without classroom', 'edusystem');
            $result['background'] = '#dfdedd';
        }

        // Short (admin) label and badge class for compact admin displays
        if ($hasMoodleAccess && $result['student_access'] === true) {
            $result['short_text'] = __('Full access', 'edusystem');
            $result['badge_class'] = 'aes-badge--success';
        } elseif ($hasMoodleAccess && $result['student_access'] === false) {
            $result['short_text'] = __('Access removed', 'edusystem');
            $result['badge_class'] = 'aes-badge--warning';
        } else {
            $result['short_text'] = __('No classroom', 'edusystem');
            $result['badge_class'] = 'aes-badge--muted';
        }

        // Subjects coursing (optional)
        $result['subjects_coursing'] = [];
        if (get_option('show_table_subjects_coursing')) {
            $projection = get_projection_by_student($student->id);
            if ($projection) {
                $projection_obj = json_decode($projection->projection);
                if (is_array($projection_obj) || is_object($projection_obj)) {
                    $result['subjects_coursing'] = array_values(array_filter((array)$projection_obj, function ($item) {
                        return isset($item->this_cut) && $item->this_cut === true;
                    }));
                }
            }
        }

        $result['admin_virtual_access'] = get_option('virtual_access');

        return $result;
    }
}

?>

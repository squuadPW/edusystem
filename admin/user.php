<?php

function delete_data_student($user_id)
{
    global $wpdb;

    // Definir nombres de tablas
    $tables = [
        'documents' => $wpdb->prefix . 'student_documents',
        'students' => $wpdb->prefix . 'students',
        'payments' => $wpdb->prefix . 'student_payments',
        'academic_projection' => $wpdb->prefix . 'student_academic_projection',
        'period_inscriptions' => $wpdb->prefix . 'student_period_inscriptions'
    ];

    // 1. Obtener todos los correos electrónicos de los estudiantes asociados a este partner_id
    $student_emails = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT email FROM {$tables['students']} WHERE partner_id = %d",
            $user_id
        )
    );

    if (!empty($student_emails)) {
        foreach ($student_emails as $email) {
            $wp_user = get_user_by('email', $email);

            if ($wp_user && $wp_user->ID !== $user_id) {
                wp_delete_user($wp_user->ID, false);
            }
        }
    }

    // Obtener IDs de estudiantes en un solo query
    $student_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT id FROM {$tables['students']} WHERE partner_id = %d",
            $user_id
        )
    );

    if (empty($student_ids)) {
        return;
    }

    // Convertir IDs a enteros y crear lista segura para SQL
    $ids = implode(',', array_map('intval', $student_ids));

    // Eliminar datos relacionados en operaciones bulk
    $wpdb->query("DELETE FROM {$tables['documents']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['payments']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['academic_projection']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['period_inscriptions']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['students']} WHERE id IN ($ids)");
}

add_action('delete_user', 'delete_data_student');
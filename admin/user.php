<?php

function delete_data_student($user_id)
{
    global $wpdb;

    // Definir nombres de tablas
    $tables = [
        'documents' => $wpdb->prefix . 'student_documents',
        'students' => $wpdb->prefix . 'students',
        'payments' => $wpdb->prefix . 'student_payments',
        'payments_log' => $wpdb->prefix . 'student_payments_log',
        'academic_projection' => $wpdb->prefix . 'student_academic_projection',
        'period_inscriptions' => $wpdb->prefix . 'student_period_inscriptions',
        'califications' => $wpdb->prefix . 'student_califications',
        'scholarship_application' => $wpdb->prefix . 'student_scholarship_application',
        'requests' => $wpdb->prefix . 'requests',
        'scholarship_assigned' => $wpdb->prefix . 'scholarship_assigned_student',
        'programs_by_student' => $wpdb->prefix . 'programs_by_student'
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
    $wpdb->query("DELETE FROM {$tables['payments_log']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['academic_projection']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['period_inscriptions']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['califications']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['scholarship_application']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['requests']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['scholarship_assigned']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['programs_by_student']} WHERE student_id IN ($ids)");
    $wpdb->query("DELETE FROM {$tables['students']} WHERE id IN ($ids)");
    delete_woocommerce_orders($user_id);
}

// Nueva función para borrar órdenes de WooCommerce
function delete_woocommerce_orders($user_id) {
    // Obtener todas las órdenes asociadas con el ID de usuario
    $customer_orders = wc_get_orders( [
        'customer' => $user_id,
        'limit' => -1,
        'status' => 'any', // Obtener órdenes con cualquier estado
    ] );

    if ( ! empty( $customer_orders ) ) {
        foreach ( $customer_orders as $order ) {
            // Borra la orden de forma permanente
            wp_delete_post( $order->get_id(), true );
        }
    }
}

add_action('delete_user', 'delete_data_student');
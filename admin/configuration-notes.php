<?php

function add_admin_form_configuration_notes_content()
{
    global $wpdb, $grade_configs;
    $table_grade_config = $wpdb->prefix . 'grade_config';

    // Handle form submission
    if (isset($_POST['save_grade_configs']) && check_admin_referer('save_grade_configs_nonce')) {
        // Process the form data
        if (isset($_POST['grade_configs']) && is_array($_POST['grade_configs'])) {
            // First, delete all existing configs
            $wpdb->query("DELETE FROM {$table_grade_config}");

            // Insert new configs
        $sort_order = 1;
        foreach ($_POST['grade_configs'] as $config) {
            if (!empty($config['min_score']) && !empty($config['literal_grade'])) {
                $wpdb->insert(
                    $table_grade_config,
                    [
                        'min_score' => floatval($config['min_score']),
                        'literal_grade' => sanitize_text_field($config['literal_grade']),
                        'calc_grade' => floatval($config['calc_grade']),
                        'sort_order' => $sort_order++
                    ],
                    ['%f', '%s', '%f', '%d']
                );
            }
        }

            echo '<div class="notice notice-success is-dismissible"><p>' . __('Grade configurations saved successfully.', 'edusystem') . '</p></div>';
        }
    }

    // Get all grade configurations
    $grade_configs = $wpdb->get_results("SELECT * FROM {$table_grade_config} ORDER BY sort_order ASC", ARRAY_A);

    include(plugin_dir_path(__FILE__) . 'templates/configuration-notes-detail.php');
}
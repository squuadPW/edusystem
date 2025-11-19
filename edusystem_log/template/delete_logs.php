<?php

    global $wpdb;
    $table_edusystem_log = $wpdb->prefix . 'edusystem_log';

    // Procesar eliminación
    if ( isset($_POST['delete_logs']) ) {
        $user_id    = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;
        $start_date = sanitize_text_field($_POST['start_date'] ?? '');
        $end_date   = sanitize_text_field($_POST['end_date'] ?? '');

        $conditions = [];

        if ($user_id) $conditions[] = $wpdb->prepare("user_id = %d", $user_id);

        if ($start_date && $end_date) {
            $conditions[] = $wpdb->prepare(
                "created_at BETWEEN %s AND %s",
                $start_date . " 00:00:00",
                $end_date . " 23:59:59"
            );
        }

        if ($conditions) {
            $where = "WHERE " . implode(" AND ", $conditions);
            $wpdb->query("DELETE FROM {$table_edusystem_log} {$where}");
            echo '<div class="updated"><p>'.__('Logs successfully deleted.', 'edusystem').'</p></div>';
        } else {
            echo '<div class="error"><p>'.__('You must select at least one criterion.', 'edusystem').'</p></div>';
        }
    }
?>

<div id="delete_logs_page" class="wrap">
    <h1><?= esc_html(__('Delete Edusystem Logs', 'edusystem')); ?></h1>

    <form method="post">

        <!-- Filtro de usuarios -->
        <div class="user-filter">
            <label for="user_id"><?= __('Select User', 'edusystem'); ?></label>
            <select id="user_id" name="user_id"></select>
            <p><?= __('Type to search for users by name or email.', 'edusystem'); ?></p>
        </div>

        <!-- Filtro de fechas -->
        <div class="date-filters">
            <div>
                <label for="start_date"><?= __('Start Date', 'edusystem'); ?></label>
                <input type="date" name="start_date" id="start_date" />
            </div>
            <div>
                <label for="end_date"><?= __('End Date', 'edusystem'); ?></label>
                <input type="date" name="end_date" id="end_date" />
            </div>
        </div>

        <!-- Botón -->
        <div class="submit-button">
            <?php submit_button(__('Delete Logs', 'edusystem'), 'delete', 'delete_logs'); ?>
        </div>
        
    </form>
</div>



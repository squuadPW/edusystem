<?php

global $wpdb;
    $table_edusystem_log = $wpdb->prefix . 'edusystem_log';

    // Procesar eliminación
    if ( isset($_POST['delete_logs']) ) {
        $user_id    = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;
        $start_date = sanitize_text_field($_POST['start_date'] ?? '');
        $end_date   = sanitize_text_field($_POST['end_date'] ?? '');

        $conditions = [];

        if ($user_id) {
            $conditions[] = $wpdb->prepare("user_id = %d", $user_id);
        }

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
            echo '<div class="updated"><p>'.__('Logs eliminados correctamente.', 'edusystem').'</p></div>';
        } else {
            echo '<div class="error"><p>'.__('Debes seleccionar al menos un criterio.', 'edusystem').'</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h1 style="margin-bottom:20px;"><?= esc_html(__('Eliminar Logs de Edusystem', 'edusystem')); ?></h1>

        <form method="post" style="background:#fff; padding:20px; border:1px solid #ccd0d4; border-radius:6px; max-width:600px;">
            
            <!-- Filtro de usuarios con Select2 AJAX -->
            <div style="margin-bottom:20px;">
                <label for="user_id" style="display:block; font-weight:bold; margin-bottom:8px;">
                    <?= __('Seleccionar Usuario', 'edusystem'); ?>
                </label>
                <select id="user_id" name="user_id" style="width:100%;"></select>
                <p style="font-size:12px; color:#666; margin-top:5px;">
                    <?= __('Escriba para buscar usuarios por nombre o correo.', 'edusystem'); ?>
                </p>
            </div>

            <!-- Filtro de fechas -->
            <div style="margin-bottom:20px; display:flex; gap:20px;">
                <div style="flex:1;">
                    <label for="start_date" style="display:block; font-weight:bold; margin-bottom:8px;">
                        <?= __('Fecha Inicio', 'edusystem'); ?>
                    </label>
                    <input type="date" name="start_date" id="start_date" style="width:100%; padding:6px;" />
                </div>
                <div style="flex:1;">
                    <label for="end_date" style="display:block; font-weight:bold; margin-bottom:8px;">
                        <?= __('Fecha Fin', 'edusystem'); ?>
                    </label>
                    <input type="date" name="end_date" id="end_date" style="width:100%; padding:6px;" />
                </div>
            </div>

            <!-- Botón -->
            <div style="margin-top:20px;">
                <?php submit_button(__('Eliminar Logs', 'edusystem'), 'delete', 'delete_logs'); ?>
            </div>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($){
        $('#user_id').select2({
            placeholder: "<?= __('Seleccione un usuario', 'edusystem'); ?>",
            allowClear: true,
            ajax: {
                url: ajaxurl, // endpoint de WP admin AJAX
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        action: 'edusystem_search_users',
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.ID,
                                text: item.display_name + ' (' + item.user_email + ')'
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    });
    </script>
    <?php
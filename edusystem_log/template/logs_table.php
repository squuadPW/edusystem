<?php 

    include_once(EDUSYSTEM_PATH . '/edusystem_log/Edusystem_Log_Table.php');
    $logs_table = new Edusystem_Log_Table();
    $logs_table->prepare_items();

    // obtiene las fechas para el filtro
    $date = isset($_GET['date']) ? $_GET['date'] : "last_month";
    $start_date = isset($_GET['startDate']) ? $_GET['startDate'] : "";
    $end_date = isset($_GET['endDate']) ? $_GET['endDate'] : "";
                
    $date_custom = '';
    if($date !== 'custom') $date_custom = 'display: none;'; 

    // Traer todos los tipos únicos
    global $wpdb;
    $types_logs = $wpdb->get_col("
        SELECT DISTINCT type
        FROM `{$wpdb->prefix}edusystem_log`
        ORDER BY type
    ");

    // Unir las llaves de la constante con el array
    $types = array_unique(array_merge(array_keys(EDUSYSTEM_TYPE_LOGS), $types_logs));

?>

<div id="edusystem_logs" class="wrap">
    <h1><?= esc_html(__('Edusystem Logs', 'edusystem')); ?></h1>

    <form method="get" class="filters_container" >

        <input type="hidden" name="page" value="<?=$_REQUEST['page']?>" />
        
        <input type="text" id="date-range" class="input-text" style="<?= $date_custom ?>" value="<?= $start_date.' to '.$end_date ?>" />

        <select id="select-date" class="woocommerce-Input input-text" data-date="<?=$date?>" onchange ="edusystem_date_filter_transactions(this.value);" >
            <option value="today" <?=  selected( $date, 'today' ); ?> ><?= __('Today', 'edusystem') ?></option>
            <option value="last_week" <?=  selected( $date, 'last_week'); ?>  ><?= __('Last Week', 'edusystem') ?></option>
            <option value="last_month" <?=  selected( $date, 'last_month' ); ?>><?= __('Last Month', 'edusystem') ?></option>
            <option value="last_3_months" <?=  selected( $date, 'last_3_months' ); ?>><?= __('Last 3 months', 'edusystem') ?></option>
            <option value="custom" <?=  selected( $date, 'custom' ); ?>><?= __('Custom', 'edusystem') ?></option>
        </select>
                             
        <select name="type" onchange ="edusystem_filters_transactions('type',this.value);">
                    
            <option value="" <?=  selected( $_GET['type'] ?? '', '' ); ?>><?= __('Select type', 'edusystem') ?></option>

            <?php foreach( $types AS $type ): ?>
                        
                <option value="<?=$type?>" <?= selected( $_GET['type'] ?? '', $type ); ?> >
                    <?= edusystem_get_log_type_label( $type ) ?>
                </option>
            <?php endforeach; ?>
                    
        </select>     

        <?php if ( empty( $_GET['user_id'] ) ): ?>
            <?php $logs_table->search_box('search', 'search_id'); ?>
        <?php endif ?>

    </form>

    <?php $logs_table->display(); ?>
</div>

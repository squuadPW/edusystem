<h2 style="font-size:24px;text-align:center;"><?= __('My courses', 'edusystem'); ?></h2>

<section class="segment" style="margin-top: 20px">
    <div class="segment-button-history active" data-option="current"><?= __('Current', 'edusystem'); ?></div>
    <div class="segment-button-history" data-option="history"><?= __('History', 'edusystem'); ?></div>
</section>

<div>
    <div id="current_tab_content" style="display: block">
        <?php if (!empty($current)): ?>
            <table
                class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
                style="margin-top:20px;">
                <thead>
                    <tr>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-type"><span
                                class="nobr"><?= __('Type', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-subject-code"><span
                                class="nobr"><?= __('Subject - Code', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-students"><span
                                class="nobr"><?= __('Students', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-section"><span
                                class="nobr"><?= __('Section', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-term"><span
                                class="nobr"><?= __('Term', 'edusystem'); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($current as $row):
                        $endpoint_url = wc_get_account_endpoint_url('teacher-course-students');
                        $query_params = array(
                            'offer_id' => $row->id, // Si tienes un ID único para la oferta
                            'status' => 'current'
                        );
                        $full_url = add_query_arg($query_params, $endpoint_url);
                        ?>
                        <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order clickable-row"
                            data-href="<?= esc_url($full_url); ?>" style="cursor: pointer;">
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-type"
                                data-title="<?= __('Type', 'edusystem'); ?>">
                                <?= ucwords($row->type) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-subject-code"
                                data-title="<?= __('Subject - Code', 'edusystem'); ?>">
                                <?= strtoupper($row->subject) . ' - ' . strtoupper($row->code_subject) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-students"
                                data-title="<?= __('Students', 'edusystem'); ?>">
                                <?= strtoupper($row->count_students) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-section"
                                data-title="<?= __('Section', 'edusystem'); ?>">
                                <?= strtoupper($row->section) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-term"
                                data-title="<?= __('Term', 'edusystem') ?>">
                                <?= strtoupper($row->code_period) . ' - ' . strtoupper($row->cut_period) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="margin: 0 auto; text-align: center; padding: 18px;">
                <span><?= __('It looks like you don\'t have any courses registered in moodle yet. Please check back shortly when you are assigned 👋', 'edusystem'); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div id="history_tab_content" style="display: none">
        <?php if (!empty($history)): ?>
            <table
                class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table"
                style="margin-top:20px;">
                <thead>
                    <tr>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-type"><span
                                class="nobr"><?= __('Type', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-subject-code"><span
                                class="nobr"><?= __('Subject - Code', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-students"><span
                                class="nobr"><?= __('Students', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-prom-califications">
                            <span class="nobr"><?= __('Prom califications', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-section"><span
                                class="nobr"><?= __('Section', 'edusystem'); ?></span></th>
                        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-term"><span
                                class="nobr"><?= __('Term', 'edusystem'); ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $row):
                        $endpoint_url = wc_get_account_endpoint_url('teacher-course-students');
                        $query_params = array(
                            'offer_id' => $row->id, // Si tienes un ID único para la oferta
                            'status' => 'history'
                        );
                        $full_url = add_query_arg($query_params, $endpoint_url);
                        ?>
                        <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order clickable-row"
                            data-href="<?= esc_url($full_url); ?>" style="cursor: pointer;">
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-type"
                                data-title="<?= __('Type', 'edusystem'); ?>">
                                <?= ucwords($row->type) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-subject-code"
                                data-title="<?= __('Subject - Code', 'edusystem'); ?>">
                                <?= strtoupper($row->subject) . ' - ' . strtoupper($row->code_subject) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-students"
                                data-title="<?= __('Students', 'edusystem'); ?>">
                                <?= strtoupper($row->count_students) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-prom-califications"
                                data-title="<?= __('Prom califications', 'edusystem'); ?>">
                                <?= strtoupper($row->prom_calification) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-section"
                                data-title="<?= __('Section', 'edusystem'); ?>">
                                <?= strtoupper($row->section) ?>
                            </td>
                            <td class="align-middle woocommerce-orders-table__cell woocommerce-orders-table__cell-term"
                                data-title="<?= __('Term', 'edusystem') ?>">
                                <?= strtoupper($row->code_period) . ' - ' . strtoupper($row->cut_period) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="margin: 0 auto; text-align: center; padding: 18px;">
                <span><?= __('Oh you don\'t have any note history that we can provide you, as soon as we have it you\'ll see it here 😁', 'edusystem'); ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        // Script para la funcionalidad de las pestañas (tabs)
        $('.segment-button-history').on('click', function () {
            var targetOption = $(this).data('option');

            // Eliminar 'active' de todos los botones y añadirlo al clicado
            $('.segment-button-history').removeClass('active');
            $(this).addClass('active');

            // Ocultar todos los contenidos de las pestañas
            $('#current_tab_content, #history_tab_content').hide();

            // Mostrar el contenido de la pestaña seleccionada
            $('#' + targetOption + '_tab_content').show();
        });

        // Script para hacer las filas de la tabla clicables
        $(".clickable-row").on("click", function () {
            var url = $(this).data("href");
            if (url) {
                window.location.href = url;
            }
        });
    });
</script>
<div>
    <div style="padding: 20px !important">
        <table class="wp-list-table widefat fixed posts striped">
            <thead>
                <tr>
                    <th>ESTUDIANTES EN HISTORIA (<?= count($history) ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br>
    <div style="padding: 20px !important">
        <table class="wp-list-table widefat fixed posts striped">
            <thead>
                <tr>
                    <th>ESTUDIANTES EN GOBIERNO (<?= count($government) ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($government as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br>
    <div style="padding: 20px !important">
        <table class="wp-list-table widefat fixed posts striped">
            <thead>
                <tr>
                    <th>ESTUDIANTES EN INGLES III (<?= count($english_tree) ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($english_tree as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br>
    <div style="padding: 20px !important">
        <table class="wp-list-table widefat fixed posts striped">
            <thead>
                <tr>
                    <th>ESTUDIANTES EN INGLES IV (<?= count($english_four) ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($english_four as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br>
    <div style="padding: 20px !important">
       <table class="wp-list-table widefat fixed posts striped">
            <thead>
                <tr>
                    <th>ESTUDIANTES EN ECONOMIA (<?= count($economic) ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($economic as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br>
    <div style="padding: 20px !important">
        <table class="wp-list-table widefat fixed posts striped">
            <thead>
                <tr>
                    <th>ESTUDIANTES EN PRE-CALCULO (<?= count($precalc) ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($precalc as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br>
</div>
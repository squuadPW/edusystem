<div>
    <form method="post"
        action="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&section_tab=validate_enrollments'); ?>">

        <div>
            <h3 style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                <b><?= __('Filter', 'aes'); ?></b>
            </h3>

            <div style="text-align: center;">
                <label for="input_id"><b><?= __('Period', 'aes'); ?></b></label><br>
                <select name="academic_period">
                    <option value="" selected>Select academic period to filter</option>
                    <?php foreach ($periods as $period) { ?>
                        <option value="<?php echo $period->code; ?>" <?= ($projection_for->code_period == $period->code) ? 'selected' : ''; ?>>
                            <?php echo $period->name; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div style="text-align: center;">
                <label for="input_id"><b><?= __('Cut', 'aes'); ?></b></label><br>
                <select name="academic_period_cut">
                    <option value="">Select academic period cut</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>

        </div>

        <div style="margin-top:20px;text-align:center">
            <button type="submit" class="button button-success" name="action" value="save"><?= __('Search', 'aes'); ?></button>
        </div>
    </form>
</div>

<div>
    <div style="padding: 20px !important">
        <table class="wp-list-table widefat fixed posts striped">
            <thead>
                <tr>
                    <th colspan="4">STUDENTS OF U. S. HISTORY (<?= count($history) ?>)</th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>SURNAMES AND NAMES</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= $key + 1 ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->id_document) ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->last_name) . ' ' . strtoupper($value['student']->middle_last_name) . ' ' . strtoupper($value['student']->name) . ' ' . strtoupper($value['student']->middle_name) ?>
                        </td>
                        <td>
                            <?= $value['calification'] ?>
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
                    <th colspan="4">STUDENTS OF U. S. GOVERNMENT (<?= count($government) ?>)</th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>SURNAMES AND NAMES</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($government as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= $key + 1 ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->id_document) ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->last_name) . ' ' . strtoupper($value['student']->middle_last_name) . ' ' . strtoupper($value['student']->name) . ' ' . strtoupper($value['student']->middle_name) ?>
                        </td>
                        <td>
                            <?= $value['calification'] ?>
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
                    <th colspan="4">STUDENTS OF ENGLISH III (<?= count($english_tree) ?>)</th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>SURNAMES AND NAMES</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($english_tree as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= $key + 1 ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->id_document) ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->last_name) . ' ' . strtoupper($value['student']->middle_last_name) . ' ' . strtoupper($value['student']->name) . ' ' . strtoupper($value['student']->middle_name) ?>
                        </td>
                        <td>
                            <?= $value['calification'] ?>
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
                    <th colspan="4">STUDENTS OF ENGLISH IV (<?= count($english_four) ?>)</th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>SURNAMES AND NAMES</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($english_four as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= $key + 1 ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->id_document) ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->last_name) . ' ' . strtoupper($value['student']->middle_last_name) . ' ' . strtoupper($value['student']->name) . ' ' . strtoupper($value['student']->middle_name) ?>
                        </td>
                        <td>
                            <?= $value['calification'] ?>
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
                    <th colspan="4">STUDENTS OF ECONOMICS & FINANCIAL LITERACY (<?= count($economic) ?>)</th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>SURNAMES AND NAMES</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($economic as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= $key + 1 ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->id_document) ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->last_name) . ' ' . strtoupper($value['student']->middle_last_name) . ' ' . strtoupper($value['student']->name) . ' ' . strtoupper($value['student']->middle_name) ?>
                        </td>
                        <td>
                            <?= $value['calification'] ?>
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
                    <th colspan="4">STUDENTS OF PRE-CALCULUS (<?= count($precalc) ?>)</th>
                </tr>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>SURNAMES AND NAMES</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($precalc as $key => $value) { ?>

                    <tr>
                        <td>
                            <?= $key + 1 ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->id_document) ?>
                        </td>
                        <td>
                            <?= strtoupper($value['student']->last_name) . ' ' . strtoupper($value['student']->middle_last_name) . ' ' . strtoupper($value['student']->name) . ' ' . strtoupper($value['student']->middle_name) ?>
                        </td>
                        <td>
                            <?= $value['calification'] ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br>
</div>
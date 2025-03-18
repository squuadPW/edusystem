<div id="template_certificate" style="display: none">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="margin-left: 10px">
            <?php include(plugin_dir_path(__FILE__) . 'img-logo.php'); ?>
        </div>
        <div style="text-align: center;">
            <div>United States of America</div>
            <div>American Elite School</div>
            <div>Miami, Florida</div>
        </div>
        <div style="text-align: center; margin-right: 10px">
            <div>Score Record P.A.:</div>
            <div>Cod. Val.: </div>
        </div>
    </div>

    <br>
    <table class="wp-list-table widefat fixed posts striped" style="margin-top: 20px">
        <thead>
            <tr>
                <th colspan="12" style="text-align: center"><strong>STUDENT DATA</strong></th>
            </tr>
            <tr>
                <th colspan="6">SUBJECT</th>
                <th colspan="6">CALIFICATION</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (json_decode($projection->projection) as $key => $projection_for) { ?>
                <tr>
                  <td colspan="6"><?= $projection_for->subject ?></td>
                  <td colspan="6"><?= $projection_for->calification ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

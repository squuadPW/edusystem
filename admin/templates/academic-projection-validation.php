<div>
    <div>
        <h4>ESTUDIANTES EN HISTORIA (<?= count($history) ?>)</h4>
        <ul>
            <?php foreach ($history as $key => $value) { ?>
                <li><?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>
    <div>
        <h4>ESTUDIANTES EN GOBIERNO (<?= count($government) ?>)</h4>
        <ul>
            <?php foreach ($government as $key => $value) { ?>
                <li><?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>
    <div>
        <h4>ESTUDIANTES EN INGLES III (<?= count($english_tree) ?>)</h4>
        <ul>
            <?php foreach ($english_tree as $key => $value) { ?>
                <li><?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>    
    <div>
        <h4>ESTUDIANTES EN INGLES IV (<?= count($english_four) ?>)</h4>
        <ul>
            <?php foreach ($english_four as $key => $value) { ?>
                <li><?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>
    <div>
        <h4>ESTUDIANTES EN ECONOMIA (<?= count($economic) ?>)</h4>
        <ul>
            <?php foreach ($economic as $key => $value) { ?>
                <li><?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>
    <div>
        <h4>ESTUDIANTES EN PRE-CALCULO (<?= count($precalc) ?>)</h4>
        <ul>
            <?php foreach ($precalc as $key => $value) { ?>
                <li><?= strtoupper($value->last_name) . ' ' . strtoupper($value->middle_last_name) . ' ' . strtoupper($value->name) . ' ' . strtoupper($value->middle_name) ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>
</div>
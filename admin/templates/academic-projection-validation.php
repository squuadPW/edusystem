<div>
    <div>
        <h4>ESTUDIANTES EN HISTORIA</h4>
        <ul>
            <?php foreach ($history as $key => $value) { ?>
                <li><?= $value->name . ' ' . $value->middle_name . ' ' . $value->last_name . ' ' . $value->middle_last_name ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>
    <div>
        <h4>ESTUDIANTES EN GOBIERNO</h4>
        <ul>
            <?php foreach ($government as $key => $value) { ?>
                <li><?= $value->name . ' ' . $value->middle_name . ' ' . $value->last_name . ' ' . $value->middle_last_name ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>
    <div>
        <h4>ESTUDIANTES EN INGLES III</h4>
        <ul>
            <?php foreach ($english_tree as $key => $value) { ?>
                <li><?= $value->name . ' ' . $value->middle_name . ' ' . $value->last_name . ' ' . $value->middle_last_name ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>    
    <div>
        <h4>ESTUDIANTES EN INGLES IV</h4>
        <ul>
            <?php foreach ($english_four as $key => $value) { ?>
                <li><?= $value->name . ' ' . $value->middle_name . ' ' . $value->last_name . ' ' . $value->middle_last_name ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>
    <div>
        <h4>ESTUDIANTES EN ECONOMIA</h4>
        <ul>
            <?php foreach ($economic as $key => $value) { ?>
                <li><?= $value->name . ' ' . $value->middle_name . ' ' . $value->last_name . ' ' . $value->middle_last_name ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>
    <div>
        <h4>ESTUDIANTES EN PRE-CALCULO</h4>
        <ul>
            <?php foreach ($precalc as $key => $value) { ?>
                <li><?= $value->name . ' ' . $value->middle_name . ' ' . $value->last_name . ' ' . $value->middle_last_name ?></li>
            <?php } ?>
        </ul>
    </div>
    <br>
</div>
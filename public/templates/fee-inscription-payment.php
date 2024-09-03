<?php
    foreach ($students as $key => $student) {
        ?>
        <form method="POST" action="<?= the_permalink().'?action=fee_inscription_payment&fee_student_id='.$student->id; ?>">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
                    <button class="submit"><?= __('Registration fee payment','aes'); ?> <br> <?= $student->name . ' ' . $student->last_name ?></button>
                </div>
            </div>
        </form>
    <?php 
    }
?>
    

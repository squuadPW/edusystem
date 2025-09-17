<?php
    foreach ($students as $key => $student) {
        ?>
        <form method="POST" action="<?= the_permalink().'?action=fee_inscription_payment&fee_student_id='.$student->id; ?>">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">
                    <button class="submit">
                        <?php
                            $user_name = $student->name . ' ' . $student->last_name;
                            $translated_text = sprintf(
                                /* translators: %s: User's name */
                                __('Pay %s\'s registration fee', 'edusystem'),
                                $user_name
                            );
                            echo $translated_text;
                        ?>
                    </button>
                </div>
            </div>
        </form>
    <?php 
    }
?>
    

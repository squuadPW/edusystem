<?php 

add_action('woocommerce_account_dashboard', function () {

    $current_user_id = get_current_user_id();
    if (!$current_user_id) return;

    $students = get_students_detail_partner($current_user_id);
    if (!$students) return;

    $html = '';
    $load = load_current_cut_enrollment();
    $code = $load['code'];
    $cut = $load['cut'];

    foreach ( $students as $student ) {
        
        $student_id = $student->id;

        global $wpdb;
        $table_inscriptions = "{$wpdb->prefix}student_period_inscriptions";
        $table_subjects = "{$wpdb->prefix}school_subjects";

        $subjects_failed = $wpdb->get_results( $wpdb->prepare(
            "SELECT `sub`.id, `sub`.name, `sub`.code_subject,
                COUNT(CASE WHEN `ins`.status_id = 4 THEN 1 END) as total_reprobadas
            FROM {$table_inscriptions} `ins`
            INNER JOIN {$table_subjects} `sub` ON `sub`.id = `ins`.subject_id
            WHERE `ins`.student_id = %d AND `sub`.type = 'elective'
            GROUP BY `sub`.id
            /* HAVING 
                AND total_reprobadas >= 4
                AND SUM(`ins`.status_id = 3) < 0; */", 
            $student_id
        ));

        echo "<pre>";
        var_dump($subjects_failed);
        echo "</pre>";

        /* if ( !$subjects_failed )  continue; 

        $subjects_remedial = [];
        foreach ( $subjects_failed as $subject ) {
            
            // verifica que la materia tiene ofertas actuales
            $offer_available_to_enroll = offer_available_to_enroll($subject->id, $code, $cut);
            if (!$offer_available_to_enroll) continue;
            
            array_push($subjects_remedial, $subject);
        }
        
        if ( !$subjects_remedial )  continue;  */

        ob_start(); 
        ?>
            <div id="buy-failed-subjects-container" class="seccion-dashboard">

                <div class="seccion-dashboard-header">
                    <h4><?= sprintf( __('Buy elective subjects by %s\'s', 'edusystem'), "{$student->name} {$student->last_name}" );?></h4>
                </div>
                
                <div class="list-failed-subjects">
                    <?php foreach ( get_subject_by_type('elective') as $subject): ?>
                        <div class="item-failed-subject" >
                                
                            <?php 
                                $add_to_cart_url = add_query_arg( 
                                    array( 
                                        'add-to-cart' => get_master_subject_product_id(), 
                                        'subject_id' => $subject->id,
                                        'student_id' => $student_id
                                    ), 
                                    wc_get_checkout_url()
                                );
                            ?>

                            <a href="<?= esc_url($add_to_cart_url); ?>" class="button button-primary button-small"><span><?= esc_html($subject->name); ?></span></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php
        $html .= ob_get_clean();
    }

    echo $html;

}, 1, 1); 
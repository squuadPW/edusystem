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

        $inscriptions = $wpdb->get_row( $wpdb->prepare(
            "SELECT 
                COUNT(DISTINCT CASE WHEN `a`.status_id = 3 THEN `a`.code_subject END) as approved,
                COUNT(CASE WHEN `a`.status_id = 4 THEN 1 END) as failed,
                MAX(CASE WHEN `a`.status_id = 3 AND EXISTS ( 
                    SELECT 1 FROM {$table_inscriptions} AS `b`
                    WHERE `b`.student_id = `a`.student_id AND `b`.status_id = 4 AND `b`.code_subject = `a`.code_subject
                ) THEN 1 ELSE 0 END) as has_repeat
            FROM {$table_inscriptions} AS `a`
            WHERE `a`.student_id = %d AND `a`.type = 'elective'",
            $student_id
        ));

        $approved = (int) $inscriptions->approved;
        $failed = (int) $inscriptions->failed;
        $has_repeat = (int) $inscriptions->has_repeat;
        if ( $has_repeat ) $failed -= 1;

        if ( $approved >= 2 || ( $approved == 1 && $failed >= 2 ) || $failed >= 4 ) continue;


        $subjects = get_subject_by_type('elective');
        foreach ( $subjects as $subject ) {

            $inscriptions_subjects = $wpdb->get_var( $wpdb->prepare(
                "SELECT id
                FROM {$table_inscriptions}
                WHERE student_id = %d AND code_subject = %s AND status_id IN (0,1,3)",
                $student_id,
                $subject->code_subject
            ));
            
            // verifica que la materia tiene ofertas actuales
            $offer_available_to_enroll = offer_available_to_enroll($subject->id, $code, $cut);
            if ( !$offer_available_to_enroll ) continue;

            echo '<pre>';
            var_dump($offer_available_to_enroll);
            echo '</pre>';
        }
        
        if ( !$subjects )  continue;  

        ob_start(); 
        /* ?>
            <div id="buy-failed-subjects-container" class="seccion-dashboard">

                <div class="seccion-dashboard-header">
                    <h4><?= sprintf( __('Buy elective subjects by %s\'s', 'edusystem'), "{$student->name} {$student->last_name}" );?></h4>
                </div>
                
                <div class="list-failed-subjects">
                    <?php foreach ( $subjects AS $subject ): ?>
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

                            <a href="<?= esc_url($add_to_cart_url); ?>" ><span><?= esc_html($subject->name); ?></span></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php */
        $html .= ob_get_clean();
    }

    echo $html;

}, 1, 1); 
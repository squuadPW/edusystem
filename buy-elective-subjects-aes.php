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

        $failed_except_approved = $failed;
        if( $has_repeat ) $failed_except_approved -= 1;    

        if ( ( $approved == 0 && $failed < 4 ) || ( $approved == 1 && $failed_except_approved < 2 ) ) continue;

        $subjects = get_subject_by_type('elective');
        foreach ( $subjects as $key => $subject ) {
            // veifica si tiene una inscripcion activa en espera op ya paso la electiva
            $inscriptions_subjects = $wpdb->get_var( $wpdb->prepare(
                "SELECT id
                FROM {$table_inscriptions}
                WHERE student_id = %d AND code_subject = %s AND status_id IN (0,1,3)",
                $student_id,
                $subject->code_subject
            ));

            if( $inscriptions_subjects ) continue;

            // verifica que la materia tiene ofertas actuales
            $offer_available_to_enroll = offer_available_to_enroll($subject->id, $code, $cut);
            if ( $offer_available_to_enroll ) continue;

            unset($subjects[$key]);
        }
        
        if ( !$subjects )  continue;  

        ob_start(); 
        ?>
            <div id="buy-failed-subjects-container" class="seccion-dashboard">

                <div class="seccion-dashboard-header">
                    <h4><?= sprintf( __('Buy elective subjects by %s\'s', 'edusystem'), "{$student->name} {$student->last_name}" );?></h4>
                </div>

                <div class="courses-grid">

                    <?php foreach ( $subjects AS $subject ): ?>

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

                        <div class="course-card">
                            
                            <?php if ( $subject->url_image ): ?>
                                <div class="course-image">
                                    <img src="<?= esc_url($subject->url_image); ?>" alt="<?= esc_attr($subject->name); ?>">
                                </div>
                            <?php endif; ?>

                            <div class="course-content">

                                <div class="course-header">
                                    <h3 class="course-title"><?= esc_html($subject->name); ?></h3>

                                    <p class="course-price" ><?= wc_price( $subject->price, [ 'currency' => $subject->currency ] ); ?></p>

                                </div>

                                <?php if ( $subject->description ): ?>
                                    <p class="course-description"><?= esc_html($subject->description); ?></p>
                                <?php endif; ?>

                                <a class="button-primary course-button" href="<?= esc_url($add_to_cart_url); ?>"><?= __('Buy course', 'edusystem') ?></a>
                            </div>

                        </div>
                        
                    <?php endforeach; ?>

         
            </div>
        <?php
        $html .= ob_get_clean();
    }

    echo $html;

    ?>

            <!-- <script>
                document.querySelectorAll('.course-card').forEach(card => {
                    card.addEventListener('click', () => {
                        const title = card.querySelector('.course-title').innerText;
                        console.log("Navegando al curso: " + title);
                        // Aquí podrías redirigir: window.location.href = '/curso-detalle';
                    });
                });
            </script> -->
    
    <?php

}, 10, 1); 
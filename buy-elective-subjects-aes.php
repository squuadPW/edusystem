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

                            $price = $subject->price;
                            if( $price == NULL ) {
                                $default_price_electives = get_option('default_price_electives', 0);
                                $price = floatval($default_price_electives);
                            } else {
                                $price = floatval($price);
                            }
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

                                    <p class="course-price" ><?= wc_price( $price, [ 'currency' => $subject->currency ] ); ?></p>

                                </div>

                                <?php if ( $subject->description ): ?>
                                    <p class="course-description"><?= esc_html($subject->description); ?></p>
                                <?php endif; ?>

                                <a class="button-primary course-button" href="<?= esc_url($add_to_cart_url); ?>"><?= __('Buy course', 'edusystem') ?></a>
                            </div>

                        </div>
                        
                    <?php endforeach; ?>

                </div>

<style>

    :root {
    --primary-color: #4f46e5;
    --text-main: #1f2937;
    --text-muted: #6b7280;
    --bg-card: #ffffff;
    --radius: 12px;
}

.course-card {
    width: 100%;
    max-width: 300px;
    background-color: var(--bg-card);
    border-radius: var(--radius);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #f3f4f6;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.course-image img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    display: block;
}

.course-content {
    padding: 1.25rem;
}

.course-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    color: var(--text-main);
    font-weight: 600;
}

.course-description {
    margin-bottom: 1.25rem;
    font-size: 0.9rem;
    color: var(--text-muted);
    line-height: 1.5;
    /* Limita a 2 líneas si es muy larga */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.course-button {
    width: 100%;
    padding: 0.6rem;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}

.course-button:hover {
    background-color: #4338ca;
}

    .courses-grid {
    display: grid;
    /* Crea columnas de mínimo 280px. Si no caben, saltan a la siguiente fila */
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem; /* Espaciado entre tarjetas */
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto; /* Centra el contenedor en la página */
}

/* Ajuste extra para que todas las tarjetas tengan la misma altura 
   incluso si unas tienen descripción y otras no */
.course-card {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.course-content {
    display: flex;
    flex-direction: column;
    flex-grow: 1; /* Empuja el botón hacia abajo para que queden alineados */
}

.course-button {
    margin-top: auto; /* Alinea los botones al final de la tarjeta */
}
</style>

<script>
    document.querySelectorAll('.course-card').forEach(card => {
        card.addEventListener('click', () => {
            const title = card.querySelector('.course-title').innerText;
            console.log("Navegando al curso: " + title);
            // Aquí podrías redirigir: window.location.href = '/curso-detalle';
        });
    });
</script>
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
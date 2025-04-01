<!-- Incluir CDN de Swiper -->
<link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">
<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<div class="swiper-container">
    <div class="swiper">
        <div class="swiper-wrapper">
            <?php foreach ($feeds as $feed): ?>
                <?php
                $desktop_url = wp_get_attachment_url($feed->attach_id_desktop);
                $mobile_url = !empty($feed->attach_id_mobile) ? wp_get_attachment_url($feed->attach_id_mobile) : $desktop_url;
                ?>
                
                <div class="swiper-slide" 
                     style="--desktop-image: url('<?php echo $desktop_url; ?>');
                            --mobile-image: url('<?php echo $mobile_url; ?>');">
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Botones de navegación -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <!-- Paginación -->
        <div class="swiper-pagination"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.swiper', {
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
});
</script>
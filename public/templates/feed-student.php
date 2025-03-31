<div class="feed-container">
    <?php if (!empty($feeds)): ?>
        <!-- Slider main container -->
        <div class="swiper">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
                <?php foreach ($feeds as $key => $feed): ?>
                    <?php
                    $desktop_url = wp_get_attachment_url($feed->attach_id_desktop);
                    $mobile_url = !empty($feed->attach_id_mobile) ? wp_get_attachment_url($feed->attach_id_mobile) : $desktop_url;
                    ?>

                    <!-- Slides -->
                    <div class="swiper-slide" style="background-image: url('<?php echo esc_url($desktop_url); ?>'); height: 250px"
                        data-mobile-bg="<?php echo esc_url($mobile_url); ?>">
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="swiper-pagination"></div>

            <!-- Navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const swiper = new Swiper('.swiper', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: true,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            }
        });

        // Cambiar entre imágenes mobile/desktop
        function handleResponsiveImages() {
            const slides = document.querySelectorAll('.swiper-slide');
            const isMobile = window.matchMedia('(max-width: 768px)').matches;

            slides.forEach(slide => {
                const mobileBg = slide.dataset.mobileBg;
                if (isMobile && mobileBg) {
                    slide.style.backgroundImage = `url('${mobileBg}')`;
                } else {
                    const desktopBg = slide.style.backgroundImage;
                    if (!desktopBg.includes('url')) {
                        slide.style.backgroundImage = `url('${slide.dataset.desktopBg}')`;
                    }
                }
            });
        }

        // Ejecutar al cargar y al cambiar tamaño
        handleResponsiveImages();
        window.addEventListener('resize', handleResponsiveImages);
    });
</script>
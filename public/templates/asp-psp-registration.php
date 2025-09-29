<?php
// Lógica de PHP al inicio del archivo
if (is_user_logged_in()) {
    global $wpdb;
    $current_user = wp_get_current_user();
    $email = $current_user->user_email;
    $table_students = $wpdb->prefix . 'students';
    $is_student = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_students WHERE email = %s", $email));
}

$supported_languages = ['en_EN', 'es_ES'];

// Prioridad 1: Obtener el idioma de la URL ($_GET)
if (isset($_GET['lang']) && in_array($_GET['lang'], $supported_languages)) {
    $current_lang = $_GET['lang'];
} 
// Prioridad 2: Si no está en la URL, obtenerlo de la cookie ($_COOKIE)
elseif (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $supported_languages)) {
    $current_lang = $_COOKIE['lang'];
} 
// Prioridad 3: Si no está en ninguna parte, usar el predeterminado
else {
    $current_lang = LANG;
}

// Prepara la cadena de texto traducible para el JavaScript
$tooltip_text = __('Please select the grade you are currently studying', 'edusystem');
?>

<?php if (!empty($title)) { ?>
    <div class="title">
        <?= $title ?>
    </div>
<?php } ?>
<div class="title" style="font-size: 24px;">
    <?= __('Applicant', 'edusystem'); ?>
</div>

<input type="hidden" id="grade_tooltip_content" value="<?= esc_attr($tooltip_text); ?>">

<?php if (is_user_logged_in()) { ?>
    <?php if (isset($is_student)) { ?>
        <?php include(plugin_dir_path(__FILE__) . 'form-register-others.php'); ?>
    <?php } else { ?>
        <section class="segment" style="margin: auto">
            <div class="segment-button active" data-option="me"><?= __('Me', 'edusystem'); ?></div>
            <div class="segment-button" data-option="others"><?= __('Others', 'edusystem'); ?></div>
        </section>
        <?php include(plugin_dir_path(__FILE__) . 'form-register-me.php'); ?>
        <?php include(plugin_dir_path(__FILE__) . 'form-register-others-not-student.php'); ?>
    <?php } ?>
<?php } else { ?>
    <?php $disable_switch_language = get_option('disable_switch_language'); ?>
    <?php if ($disable_switch_language !== 'on') { ?>
        <section class="segment lang-selector" style="margin: auto">
            <a href="?lang=en_EN" class="segment-button <?= ($current_lang === 'en_EN') ? 'active' : ''; ?>" data-option="en">
                <?= __('English', 'edusystem'); ?>
            </a>
            <a href="?lang=es_ES" class="segment-button <?= ($current_lang === 'es_ES') ? 'active' : ''; ?>" data-option="es">
                <?= __('Spanish', 'edusystem'); ?>
            </a>
        </section>
    <?php } ?>
    
    <?php include(plugin_dir_path(__FILE__) . 'form-register.php'); ?>
<?php } ?>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
    const tooltipText = document.getElementById('grade_tooltip_content').value;
    tippy('#grade_tooltip', {
        content: tooltipText,
    });
</script>
<?php

function add_role_form_plugin()
{

    add_role('parent', __('Parent', 'edusystem'), [
        'read' => true,
        'upload_files' => true,
        'unfiltered_upload' => true
    ]);

    add_role('student', __('Student', 'edusystem'), [
        'read' => true,
        'upload_files' => true,
        'unfiltered_upload' => true
    ]);

    add_role('institutes', __('Institutes', 'edusystem'), [
        'read' => true,
        'edit_posts' => true,
        'upload_files' => true,
        'unfiltered_upload' => true
    ]);

    add_role('alliance', __('Alliance', 'edusystem'), [
        'read' => true,
        'edit_posts' => true,
        'upload_files' => true,
        'unfiltered_upload' => true
    ]);


}

add_action("admin_init", "add_role_form_plugin");


function remove_menu_pages_roles()
{
    global $wpdb, $current_user;
    $roles = $current_user->roles;
    $table_departments = $wpdb->prefix . 'departments';

    $departments = $wpdb->get_results("SELECT * FROM {$table_departments}");

    foreach ($departments as $department) {

        $role_name = str_replace('', '_', $department->name);
        $role_obj = get_role($role_name);
        $capabilities = $role_obj->capabilities;

        if (in_array($role_name, $roles)) {
            remove_menu_page('edit-comments.php');
            remove_menu_page('tools.php');
            remove_menu_page('profile.php');
            remove_menu_page('index.php');
            remove_menu_page('edit.php');
            remove_menu_page('options-general.php');
            remove_menu_page('wp-mail-smtp');
            remove_menu_page('wpfront-user-role-editor-all-roles');
            remove_menu_page('menu-support-ticket');

            if (!in_array('manager_users_aes', $capabilities)) {
                remove_menu_page('users.php');
            }

            if (!in_array('manager_media_aes', $capabilities)) {
                remove_menu_page('upload.php');
            }
        }
    }

    if (in_array('institutes', $roles) || in_array('alliance', $roles)) {
        remove_menu_page('edit-comments.php');
        remove_menu_page('tools.php');
        remove_menu_page('profile.php');
        remove_menu_page('index.php');
        remove_menu_page('edit.php');
        remove_menu_page('upload.php');
        remove_menu_page('options-general.php');
        remove_menu_page('wp-mail-smtp');
        remove_menu_page('menu-support-ticket');
        remove_menu_page('users.php');
        remove_menu_page('upload.php');
    }
}

add_action('admin_menu', 'remove_menu_pages_roles', 99);

function remove_items_top_menu_bar($wp_admin_bar)
{

    global $current_user, $wpdb;
    $roles = $current_user->roles;
    $table_departments = $wpdb->prefix . 'departments';

    $departments = $wpdb->get_results("SELECT * FROM {$table_departments}");

    foreach ($departments as $department) {

        $role_name = str_replace('', '_', $department->name);

        if (in_array($role_name, $roles)) {

            $wp_admin_bar->remove_node('comments');
            $wp_admin_bar->remove_node('new-content');
            $wp_admin_bar->remove_node('wpseo-menu');
            $wp_admin_bar->remove_node('bar-archive');
            $wp_admin_bar->remove_node('site-name');
            $wp_admin_bar->remove_node('wp-logo');
        }
    }

    if (in_array('institutes', $roles) || in_array('alliance', $roles)) {

        $wp_admin_bar->remove_node('comments');
        $wp_admin_bar->remove_node('new-content');
        $wp_admin_bar->remove_node('wpseo-menu');
        $wp_admin_bar->remove_node('bar-archive');
        $wp_admin_bar->remove_node('site-name');
        $wp_admin_bar->remove_node('wp-logo');
    }
}

add_action('admin_bar_menu', 'remove_items_top_menu_bar', 800);

function page_profile()
{

    global $current_user, $wpdb;
    $table_departments = $wpdb->prefix . 'departments';
    $roles = $current_user->roles;

    $departments = $wpdb->get_results("SELECT * FROM {$table_departments}");

    foreach ($departments as $department) {

        $role_name = str_replace('', '_', $department->name);

        if (in_array($role_name, $roles)) {
            remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
        }
    }
}

add_action('admin_head', 'page_profile');

if (!function_exists('cor_remove_personal_options')) {

    function cor_remove_personal_options($subject)
    {

        global $current_user, $wpdb;
        $roles = $current_user->roles;
        $table_departments = $wpdb->prefix . 'departments';

        $departments = $wpdb->get_results("SELECT * FROM {$table_departments}");

        foreach ($departments as $department) {

            $role_name = str_replace('', '_', $department->name);

            if (in_array($role_name, $roles)) {

                $subject = preg_replace('#<h2>' . __("Personal Options") . '</h2>#s', '<style>#your-profile>.form-table {display:none} #your-profile>.form-table ~ .form-table {display:block}</style>', $subject, 1);
                $subject = preg_replace('#<h2>Name</h2>#s', '', $subject, 1);
                $subject = preg_replace('#<tr class="user-user-login-wrap(.*?)</tr>#s', '', $subject, 1);
                $subject = preg_replace('#<tr class="user-display-name-wrap(.*?)</tr>#s', '', $subject, 1);
                //$subject = preg_replace('#<tr class="user-nickname-wrap(.*?)</tr>#s', '', $subject, 1); 
                $subject = preg_replace('#<h2>' . __("Contact Info") . '</h2>#s', '', $subject, 1);
                //$subject = preg_replace('#<tr class="user-email-wrap(.*?)</tr>#s', '', $subject, 1); 
                $subject = preg_replace('#<tr class="user-url-wrap(.*?)</tr>#s', '', $subject, 1);
                $subject = preg_replace('#<h2>' . __("About Yourself") . '</h2>#s', '', $subject, 1);
                $subject = preg_replace('#<tr class="user-description-wrap(.*?)</tr>#s', '', $subject, 1);
                $subject = preg_replace('#<tr class="user-profile-picture(.*?)</tr>#s', '', $subject, 1);
                $subject = preg_replace('#<tr class="user-sessions-wrap hide-if-no-js(.*?)</tr>#s', '', $subject, 1);
                $subject = preg_replace('#<h2>' . __('Application Passwords') . '</h2>#s', '', $subject, 1);
                $subject = preg_replace('#<button type="button" name="do_new_application_password" id="do_new_application_password" class="button button-secondary">' . __('Add New Application Password') . '</button>#s', '', $subject, 1);
            }
        }

        if (in_array('institutes', $roles) || in_array('alliance', $roles)) {

            $subject = preg_replace('#<h2>' . __("Personal Options") . '</h2>#s', '<style>#your-profile>.form-table {display:none} #your-profile>.form-table ~ .form-table {display:block}</style>', $subject, 1);
            $subject = preg_replace('#<h2>Name</h2>#s', '', $subject, 1);
            $subject = preg_replace('#<tr class="user-user-login-wrap(.*?)</tr>#s', '', $subject, 1);
            $subject = preg_replace('#<tr class="user-display-name-wrap(.*?)</tr>#s', '', $subject, 1);
            //$subject = preg_replace('#<tr class="user-nickname-wrap(.*?)</tr>#s', '', $subject, 1); 
            $subject = preg_replace('#<h2>' . __("Contact Info") . '</h2>#s', '', $subject, 1);
            //$subject = preg_replace('#<tr class="user-email-wrap(.*?)</tr>#s', '', $subject, 1); 
            $subject = preg_replace('#<tr class="user-url-wrap(.*?)</tr>#s', '', $subject, 1);
            $subject = preg_replace('#<h2>' . __("About Yourself") . '</h2>#s', '', $subject, 1);
            $subject = preg_replace('#<tr class="user-description-wrap(.*?)</tr>#s', '', $subject, 1);
            $subject = preg_replace('#<tr class="user-profile-picture(.*?)</tr>#s', '', $subject, 1);
            $subject = preg_replace('#<tr class="user-sessions-wrap hide-if-no-js(.*?)</tr>#s', '', $subject, 1);
            $subject = preg_replace('#<h2>' . __('Application Passwords') . '</h2>#s', '', $subject, 1);
            $subject = preg_replace('#<button type="button" name="do_new_application_password" id="do_new_application_password" class="button button-secondary">' . __('Add New Application Password') . '</button>#s', '', $subject, 1);
        }

        return $subject;
    }

    function cor_profile_subject_start()
    {
        ob_start('cor_remove_personal_options');
    }

    function cor_profile_subject_end()
    {
        ob_end_flush();
    }
}

add_action('admin_head', 'cor_profile_subject_start');
add_action('admin_footer', 'cor_profile_subject_end');

function wpdocs_remove_dashboard_widgets_optimized()
{
    // Widgets de WordPress por defecto
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');      // Estado del sitio y actividad (anteriormente "De un vistazo")
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Comentarios recientes
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // Enlaces entrantes (ya no se usa en versiones recientes de WP)
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');         // Plugins (ya no se usa en versiones recientes de WP)
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');       // Borrador rápido
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');     // Borradores recientes
    remove_meta_box('dashboard_primary', 'dashboard', 'side');           // Noticias y eventos de WordPress (anteriormente blog de WordPress)
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');         // Otras noticias de WordPress (ya no se usa en versiones recientes de WP)

    // Widgets comunes de WooCommerce
    remove_meta_box('wc_admin_dashboard_setup', 'dashboard', 'normal');  // Configuración de WooCommerce (puede ser 'normal' o 'side')
    remove_meta_box('woocommerce_dashboard_status', 'dashboard', 'normal'); // Estado de la tienda WooCommerce (puede ser 'normal' o 'side')
    remove_meta_box('woocommerce_dashboard_recent_reviews', 'dashboard', 'normal'); // Estado de la tienda WooCommerce (puede ser 'normal' o 'side')

    // Widgets de salud del sitio y actividad (introducidos en WP 5.2+)
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');     // Salud del sitio
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');        // Actividad (normalmente 'normal', no 'side')

    // Ejemplo de widget de plugin de terceros (ej. WP Mail SMTP)
    remove_meta_box('wp_mail_smtp_reports_widget_lite', 'dashboard', 'normal'); // Reportes de WP Mail SMTP (puede ser 'normal' o 'side')

    // Para un network dashboard (Multisitio)
    // remove_meta_box('dashboard_right_now', 'dashboard-network', 'normal');
    // remove_meta_box('dashboard_recent_comments', 'dashboard-network', 'normal');
    // remove_meta_box('dashboard_recent_drafts', 'dashboard-network', 'side');
}

add_action('wp_dashboard_setup', 'wpdocs_remove_dashboard_widgets_optimized', 99999999999);
// add_filter('screen_options_show_screen', '__return_false');

function replace_howdy($wp_admin_bar)
{
    $my_account = $wp_admin_bar->get_node('my-account');

    // Verifica si $my_account no es null antes de continuar
    if ($my_account) {
        $greeting = str_replace('Howdy,', '', $my_account->title);
        $wp_admin_bar->add_node(array(
            'id' => 'my-account',
            'title' => $greeting,
        ));
    }
}
add_filter('admin_bar_menu', 'replace_howdy', 25);
function remove_admin_bar()
{

    global $wpdb;
    $table_departments = $wpdb->prefix . 'departments';

    if (is_user_logged_in()) {

        $user = get_userdata(get_current_user_id());
        $roles = $user->roles;

        $departments = $wpdb->get_results("SELECT * FROM {$table_departments}");

        foreach ($departments as $department) {

            $role_name = str_replace('', '_', $department->name);

            if (in_array($role_name, $roles)) {
                show_admin_bar(false);
            }
        }
    }
}

add_action('after_setup_theme', 'remove_admin_bar');

function redirect_alliance_and_institute()
{

    if (get_option('disabled_redirect') && get_option('disabled_redirect') == 'on') {
        return;
    }

    global $current_user;
    $roles = $current_user->roles;

    if (is_user_logged_in()) {

        if (is_account_page()) {

            if (in_array('institutes', $roles) || in_array('alliance', $roles)) {

                wp_redirect(admin_url());
                exit;
            }
        }
    }
}

add_action('template_redirect', 'redirect_alliance_and_institute');

function admin_redirects()
{

    if (get_option('disabled_redirect') && get_option('disabled_redirect') == 'on') {
        return;
    }

    global $pagenow, $current_user;
    $user_roles = $current_user->roles;

    if ($pagenow == 'index.php') {

        if (in_array('institutes', $user_roles)) {
            wp_redirect(admin_url('admin.php?page=list_admin_institutes_student_registered_content'));
            exit;
        } else if (in_array('alliance', $user_roles)) {
            wp_redirect(admin_url('admin.php?page=list_admin_institutes_partner_registered_content'));
            exit;
        }
    }
}

add_action('admin_init', 'admin_redirects');
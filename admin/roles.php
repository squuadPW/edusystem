<?php

function add_role_form_plugin(){

    add_role('parent',__('Parent','aes'),[
        'read' => true,
        'upload_files' => true,
        'unfiltered_upload' => true
    ]);

    add_role('student',__('Student','aes'),[
        'read' => true,
        'upload_files' => true,
        'unfiltered_upload' => true
    ]);

    add_role('institutes',__('Institutes','aes'),[
        'read' => true,
        'edit_posts' => true,
        'upload_files' => true,
        'unfiltered_upload' => true
    ]);

    add_role('alliance',__('Alliance','aes'),[
        'read' => true,
        'edit_posts' => true,
        'upload_files' => true,
        'unfiltered_upload' => true
    ]);

    
}

add_action("admin_init", "add_role_form_plugin");


function remove_menu_pages_roles(){

    global $wpdb,$current_user;
    $roles = $current_user->roles;
    $table_departments = $wpdb->prefix.'departments';

    $departments = $wpdb->get_results("SELECT * FROM {$table_departments}");

    foreach($departments as $department){

        $role_name = str_replace('','_',$department->name);

        if(in_array($role_name,$roles)){
            remove_menu_page('edit-comments.php');
            remove_menu_page('tools.php');
            remove_menu_page('users.php');
            remove_menu_page('profile.php');
            remove_menu_page('index.php');
            remove_menu_page('edit.php');
            remove_menu_page('upload.php');
            remove_menu_page('options-general.php');
            remove_menu_page('wp-mail-smtp');
        }
    }

    if(in_array('institutes',$roles) || in_array('alliance',$roles)){

        remove_menu_page('edit-comments.php');
        remove_menu_page('tools.php');
        remove_menu_page('users.php');
        remove_menu_page('profile.php');
        remove_menu_page('index.php');
        remove_menu_page('edit.php');
        remove_menu_page('upload.php');
        remove_menu_page('options-general.php');
        remove_menu_page('wp-mail-smtp');

    }
}

add_action( 'admin_menu', 'remove_menu_pages_roles',99);

function remove_items_top_menu_bar($wp_admin_bar){

    global $current_user,$wpdb;
    $roles = $current_user->roles;
    $table_departments = $wpdb->prefix.'departments';

    $departments = $wpdb->get_results("SELECT * FROM {$table_departments}");

    foreach($departments as $department){

        $role_name = str_replace('','_',$department->name);

        if(in_array($role_name,$roles)){
            
            $wp_admin_bar->remove_node('comments');
            $wp_admin_bar->remove_node('new-content');
            $wp_admin_bar->remove_node('wpseo-menu');
            $wp_admin_bar->remove_node('bar-archive');
            $wp_admin_bar->remove_node('site-name');
            $wp_admin_bar->remove_node('wp-logo');
        }
    }

    if(in_array('institutes',$roles) || in_array('alliance',$roles)){

        $wp_admin_bar->remove_node('comments');
        $wp_admin_bar->remove_node('new-content');
        $wp_admin_bar->remove_node('wpseo-menu');
        $wp_admin_bar->remove_node('bar-archive');
        $wp_admin_bar->remove_node('site-name');
        $wp_admin_bar->remove_node('wp-logo');
    }
}

add_action('admin_bar_menu','remove_items_top_menu_bar',800);

function page_profile(){

    global $current_user,$wpdb;
    $table_departments = $wpdb->prefix.'departments';
    $roles = $current_user->roles;

    $departments = $wpdb->get_results("SELECT * FROM {$table_departments}");

    foreach($departments as $department){

        $role_name = str_replace('','_',$department->name);

        if(in_array($role_name,$roles)){
            remove_action('admin_color_scheme_picker','admin_color_scheme_picker');
        }
    }
}

add_action( 'admin_head', 'page_profile');

if ( ! function_exists( 'cor_remove_personal_options' ) ) {
   
    function cor_remove_personal_options( $subject ){

        global $current_user,$wpdb;
        $roles = $current_user->roles;
        $table_departments = $wpdb->prefix.'departments';
        
        $departments = $wpdb->get_results("SELECT * FROM {$table_departments}");

        foreach($departments as $department){

            $role_name = str_replace('','_',$department->name);
    
            if(in_array($role_name,$roles)){

                $subject = preg_replace( '#<h2>'.__("Personal Options").'</h2>#s', '<style>#your-profile>.form-table {display:none} #your-profile>.form-table ~ .form-table {display:block}</style>', $subject, 1 );
                $subject = preg_replace('#<h2>Name</h2>#s','',$subject,1);
                $subject = preg_replace('#<tr class="user-user-login-wrap(.*?)</tr>#s', '', $subject, 1);
                $subject = preg_replace('#<tr class="user-display-name-wrap(.*?)</tr>#s', '', $subject, 1);
                //$subject = preg_replace('#<tr class="user-nickname-wrap(.*?)</tr>#s', '', $subject, 1); 
                $subject = preg_replace('#<h2>'.__("Contact Info").'</h2>#s', '', $subject, 1);
                //$subject = preg_replace('#<tr class="user-email-wrap(.*?)</tr>#s', '', $subject, 1); 
                $subject = preg_replace('#<tr class="user-url-wrap(.*?)</tr>#s', '', $subject, 1); 
                $subject = preg_replace('#<h2>'.__("About Yourself").'</h2>#s', '', $subject, 1);
                $subject = preg_replace('#<tr class="user-description-wrap(.*?)</tr>#s', '', $subject, 1); 
                $subject = preg_replace('#<tr class="user-profile-picture(.*?)</tr>#s', '', $subject, 1);
                $subject = preg_replace('#<tr class="user-sessions-wrap hide-if-no-js(.*?)</tr>#s', '', $subject, 1);
                $subject = preg_replace('#<h2>'.__('Application Passwords').'</h2>#s','',$subject,1);
                $subject = preg_replace('#<button type="button" name="do_new_application_password" id="do_new_application_password" class="button button-secondary">'.__('Add New Application Password').'</button>#s', '', $subject, 1);   
            }
        }

        if(in_array('institutes',$roles) || in_array('alliance',$roles)){

            $subject = preg_replace( '#<h2>'.__("Personal Options").'</h2>#s', '<style>#your-profile>.form-table {display:none} #your-profile>.form-table ~ .form-table {display:block}</style>', $subject, 1 );
            $subject = preg_replace('#<h2>Name</h2>#s','',$subject,1);
            $subject = preg_replace('#<tr class="user-user-login-wrap(.*?)</tr>#s', '', $subject, 1);
            $subject = preg_replace('#<tr class="user-display-name-wrap(.*?)</tr>#s', '', $subject, 1);
            //$subject = preg_replace('#<tr class="user-nickname-wrap(.*?)</tr>#s', '', $subject, 1); 
            $subject = preg_replace('#<h2>'.__("Contact Info").'</h2>#s', '', $subject, 1);
            //$subject = preg_replace('#<tr class="user-email-wrap(.*?)</tr>#s', '', $subject, 1); 
            $subject = preg_replace('#<tr class="user-url-wrap(.*?)</tr>#s', '', $subject, 1); 
            $subject = preg_replace('#<h2>'.__("About Yourself").'</h2>#s', '', $subject, 1);
            $subject = preg_replace('#<tr class="user-description-wrap(.*?)</tr>#s', '', $subject, 1); 
            $subject = preg_replace('#<tr class="user-profile-picture(.*?)</tr>#s', '', $subject, 1);
            $subject = preg_replace('#<tr class="user-sessions-wrap hide-if-no-js(.*?)</tr>#s', '', $subject, 1);
            $subject = preg_replace('#<h2>'.__('Application Passwords').'</h2>#s','',$subject,1);
            $subject = preg_replace('#<button type="button" name="do_new_application_password" id="do_new_application_password" class="button button-secondary">'.__('Add New Application Password').'</button>#s', '', $subject, 1);   
        }
    
        return $subject;
    }

    function cor_profile_subject_start() {
        ob_start( 'cor_remove_personal_options' );
    }
    
    function cor_profile_subject_end() {
        ob_end_flush();
    }
}

add_action( 'admin_head', 'cor_profile_subject_start' );
add_action( 'admin_footer', 'cor_profile_subject_end' );

add_action('wp_dashboard_setup', 'wpdocs_remove_dashboard_widgets');

function wpdocs_remove_dashboard_widgets(){
   remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
   remove_meta_box('dashboard_right_now','dashboard','side');
}

add_filter('screen_options_show_screen', '__return_false');

function add_logo_dashboard(){
    echo '<style>

    #toplevel_page_logo_based_menu:hover{
        background-color: white !important;
    }

    @media screen and (min-width:992px){

        #toplevel_page_logo_based_menu {
            background-image: url('.plugins_url('aes').'/admin/assets/img/logo-aes.png'.');
            background-size: contain;
            background-repeat:no-repeat;
            margin:10px !important;
            background-color: white;
        }

        #toplevel_page_logo_based_menu:hover{
             background-color: white;
        }
    }

    @media screen and (min-width:783px) and (max-width:991px){

        #toplevel_page_logo_based_menu {
            background-image: url('.plugins_url('aes').'/admin/assets/img/logo-icon.png'.');
            background-size: contain;
            background-repeat:no-repeat;
            margin-bottom:5px !important;
            background-color: white !important;
        }
    }



    @media screen and (max-width:782px){

        #toplevel_page_logo_based_menu {
            background-image: url('.plugins_url('aes').'/admin/assets/img/logo-aes.png'.');
            background-size: contain;
            background-repeat:no-repeat;
            margin:10px !important;
            width:90% !important;
             background-color: white !important;
        }
    }

  

    a.wp-first-item.wp-not-current-submenu.menu-top.menu-icon-generic.toplevel_page_logo_based_menu.menu-top-last{
        visibility:hidden;
    }
    
    a.toplevel_page_logo_based_menu{
        visibility: hidden !important;
    }
 </style>';
}

add_action('admin_enqueue_scripts', 'add_logo_dashboard');

function replace_howdy( $wp_admin_bar ) {
    $my_account = $wp_admin_bar->get_node( 'my-account' );
    $greeting = str_replace( 'Howdy,', '', $my_account->title );
    $wp_admin_bar->add_node( array(
    'id' => 'my-account',
    'title' => $greeting,
    ) );
}
add_filter( 'admin_bar_menu', 'replace_howdy', 25 );

function remove_admin_bar(){

    global $wpdb;
    $table_departments = $wpdb->prefix.'departments';

    if(is_user_logged_in()){

        $user = get_userdata(get_current_user_id());
        $roles = $user->roles;

        $departments = $wpdb->get_results("SELECT * FROM {$table_departments}");

        foreach($departments as $department){

            $role_name = str_replace('','_',$department->name);
    
            if(in_array($role_name,$roles)){
                show_admin_bar(false);
            }
        }
    }
}

add_action('after_setup_theme', 'remove_admin_bar');

function redirect_alliance_and_institute(){

    global $current_user;
    $roles = $current_user->roles;

    if(is_user_logged_in()){

        if(is_account_page()){

            if(in_array('institutes',$roles) || in_array('alliance',$roles)){

                wp_redirect(admin_url());
                exit;
            }
        }        
    }


}

add_action('template_redirect','redirect_alliance_and_institute');

function admin_redirects(){

    global $pagenow,$current_user;
    $user_roles = $current_user->roles;

    if($pagenow == 'index.php'){

        if(in_array('institutes',$user_roles)){
            wp_redirect(admin_url('admin.php?page=list_admin_institutes_student_registered_content'));
            exit;
        }else if(in_array('alliance',$user_roles)){
            wp_redirect(admin_url('admin.php?page=list_admin_institutes_partner_registered_content'));
            exit;
        } 
    }
}

add_action('admin_init', 'admin_redirects');
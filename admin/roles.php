<?php

function add_role_form_plugin(){

    add_role('partner',__('Partner','restaurant-system-app'),[
        'read' => true,
    ]);
}

add_action("admin_init", "add_role_form_plugin");
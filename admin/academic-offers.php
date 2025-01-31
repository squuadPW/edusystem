<?php

function add_admin_form_academic_offers_content()
{
    if (isset($_GET['section_tab']) && !empty($_GET['section_tab'])) {
        if ($_GET['section_tab'] == 'enrollment_details') {

        }

        if ($_GET['section_tab'] == 'add_enrollment') {

        }

    } else {
        if ($_GET['action'] == 'save_enrollment_details') {

        } else {

        }
    }
}
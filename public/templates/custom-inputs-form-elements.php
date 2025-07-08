<?php
// custom-inputs-form-elements.php

// Función auxiliar para generar un input de tipo 'input'
function generate_input_field($input) {
    $input_type = isset($input->input_type) ? esc_attr($input->input_type) : '';
    $input_name = esc_attr($input->input_name);
    $input_id = esc_attr($input->input_id);
    $input_required_attr = (int) $input->input_required === 1 ? 'required' : '';
    $input_options_str = isset($input->input_options) ? esc_attr($input->input_options) : '';
    $options_array = array_map('trim', explode(',', $input_options_str));

    $html = '';

    if ($input_type === 'radio' || $input_type === 'checkbox') {
        if (!empty($options_array) && $options_array[0] !== '') {
            foreach ($options_array as $option) {
                $option_value = esc_attr($option);
                $radio_checkbox_name = $input_name;
                if ($input_type === 'checkbox') {
                    $radio_checkbox_name .= '[]';
                }

                $html .= '<div class="' . esc_attr($input_type) . '-option" style="display: flex; align-items: center; margin-top: 5px;">';
                $html .= '<input type="' . $input_type . '" id="' . $input_id . '_' . sanitize_title($option_value) . '" name="' . $radio_checkbox_name . '" value="' . $option_value . '" ' . $input_required_attr . ' style="margin-right: 8px;">';
                $html .= '<label for="' . $input_id . '_' . sanitize_title($option_value) . '">' . esc_html($option) . '</label>';
                $html .= '</div>';
            }
        } else {
            $html .= '<input type="' . $input_type . '" id="' . $input_id . '" name="' . $input_name . '" value="" ' . $input_required_attr . ' class="formdata">';
        }
    } else {
        $html .= '<input type="' . $input_type . '" id="' . $input_id . '" name="' . $input_name . '" value="" ' . $input_required_attr . ' class="formdata capitalize" autocomplete="off">';
    }
    return $html;
}

// Función auxiliar para generar un select
function generate_select_field($input) {
    $input_name = esc_attr($input->input_name);
    $input_id = esc_attr($input->input_id);
    $input_required_attr = (int) $input->input_required === 1 ? 'required' : '';
    $input_options_str = isset($input->input_options) ? esc_attr($input->input_options) : '';
    $options_array = array_map('trim', explode(',', $input_options_str));

    $html = '<select id="' . $input_id . '" name="' . $input_name . '" ' . $input_required_attr . ' autocomplete="off">';
    $html .= '<option value="">' . __('Select an option', 'edusystem') . '</option>';
    if (!empty($options_array) && $options_array[0] !== '') {
        foreach ($options_array as $option) {
            $option_value = esc_attr($option);
            $html .= '<option value="' . $option_value . '">' . esc_html($option) . '</option>';
        }
    }
    $html .= '</select>';
    return $html;
}

// Función auxiliar para generar un textarea
function generate_textarea_field($input) {
    $input_name = esc_attr($input->input_name);
    $input_id = esc_attr($input->input_id);
    $input_required_attr = (int) $input->input_required === 1 ? 'required' : '';

    $html = '<textarea id="' . $input_id . '" name="' . $input_name . '" ' . $input_required_attr . ' rows="4" cols="50" class="formdata"></textarea>';
    return $html;
}

// Inicio del formulario
if ($use_form) {
    echo '<form method="POST" action="' . $action . '" class="form-aes" autocomplete="off">';
    echo '<div class="grid grid-cols-12 gap-4">';
}

if (!empty($custom_inputs_list)) {
    foreach ($custom_inputs_list as $input) {
        $label = esc_html($input->label);
        $input_mode = esc_attr($input->input_mode);
        $input_id = esc_attr($input->input_id);
        $input_required_attr = (int) $input->input_required === 1 ? 'required' : '';

        echo '<div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" style="margin-bottom: 1rem;">';
        echo '<label for="' . $input_id . '">' . $label . (!empty($input_required_attr) ? '<span class="required">*</span>' : '') . '</label><br>';

        switch ($input_mode) {
            case 'input':
                echo generate_input_field($input);
                break;
            case 'select':
                echo generate_select_field($input);
                break;
            case 'textarea':
                echo generate_textarea_field($input);
                break;
            default:
                echo '<p style="color:red;">' . __('Unsupported input mode:', 'edusystem') . ' ' . esc_html($input_mode) . '</p>';
                break;
        }
        echo '</div>'; // Close col div
    }
}

// Cierre del formulario
if ($use_form) {
    echo '<div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3" style="text-align:center;">';
    echo "<button class='submit' id='buttonsave'>" . __('Continue', 'edusystem') . "</button>";
    echo '</div>';
    echo '</div>';
    echo '</form>';
}
?>
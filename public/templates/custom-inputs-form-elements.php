<?php
if (!empty($custom_inputs_list)) {
    foreach ($custom_inputs_list as $input) {
        // Sanitize and escape input properties
        $label = esc_html($input->label);
        $input_mode = esc_attr($input->input_mode);
        $input_type = isset($input->input_type) ? esc_attr($input->input_type) : '';
        $input_name = esc_attr($input->input_name);
        $input_id = esc_attr($input->input_id);
        $input_required_attr = (int) $input->input_required === 1 ? 'required' : '';
        $input_options_str = isset($input->input_options) ? esc_attr($input->input_options) : '';
        $options_array = array_map('trim', explode(',', $input_options_str));

        // Wrap each custom input and add margin-bottom for spacing
        echo '<div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6" style="margin-bottom: 1rem;">';
        
        // Label without bold tags
        echo '<label for="' . $input_id . '">' . $label . (!empty($input_required_attr) ? '<span class="required">*</span>' : '') . '</label><br>';

        switch ($input_mode) {
            case 'input':
                if ($input_type === 'radio' || $input_type === 'checkbox') {
                    if (!empty($options_array) && $options_array[0] !== '') {
                        foreach ($options_array as $option) {
                            $option_value = esc_attr($option);
                            $radio_checkbox_name = $input_name;
                            if ($input_type === 'checkbox') {
                                $radio_checkbox_name .= '[]';
                            }

                            // Use flexbox for left alignment of checkbox/radio
                            echo '<div class="' . esc_attr($input_type) . '-option" style="display: flex; align-items: center; margin-top: 5px;">';
                            // Changed order: input before label
                            echo '<input type="' . $input_type . '" id="' . $input_id . '_' . sanitize_title($option_value) . '" name="' . $radio_checkbox_name . '" value="' . $option_value . '" ' . $input_required_attr . ' style="margin-right: 8px;">';
                            echo '<label for="' . $input_id . '_' . sanitize_title($option_value) . '">' . esc_html($option) . '</label>';
                            echo '</div>';
                        }
                    } else {
                        echo '<input type="' . $input_type . '" id="' . $input_id . '" name="' . $input_name . '" value="" ' . $input_required_attr . ' class="formdata">';
                    }
                } else {
                    echo '<input type="' . $input_type . '" id="' . $input_id . '" name="' . $input_name . '" value="" ' . $input_required_attr . ' class="formdata capitalize" autocomplete="off">';
                }
                break;
            case 'select':
                echo '<select id="' . $input_id . '" name="' . $input_name . '" ' . $input_required_attr . ' autocomplete="off">';
                echo '<option value="">' . __('Select an option', 'edusystem') . '</option>';
                if (!empty($options_array) && $options_array[0] !== '') {
                    foreach ($options_array as $option) {
                        $option_value = esc_attr($option);
                        echo '<option value="' . $option_value . '">' . esc_html($option) . '</option>';
                    }
                }
                echo '</select>';
                break;
            case 'textarea':
                echo '<textarea id="' . $input_id . '" name="' . $input_name . '" ' . $input_required_attr . ' rows="4" cols="50" class="formdata"></textarea>';
                break;
            default:
                echo '<p style="color:red;">' . __('Unsupported input mode:', 'edusystem') . ' ' . esc_html($input_mode) . '</p>';
                break;
        }
        echo '</div>'; // Close col div
    }
}
?>
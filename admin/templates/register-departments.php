<?php
function form_departaments()
{
    ob_start();

    if (isset($_POST['name']) && isset($_POST['description'])) {
        // Get the form data
        $name = sanitize_text_field($_POST['name']);
        $description = sanitize_text_field($_POST['description']);
    
        // Create a new table if it doesn't exist
        global $wpdb;
        $table_name = $wpdb->prefix. 'departments';
    
        // Insert form data into the table
        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'description' => $description
            )
        );
    
        // Display a success message
        echo '<div style="text-align: center;
            padding: 10px;
            background-color: #b9e3b9;
            border-radius: 10px;
            margin: 10px;">
            Form data saved successfully!
        </div>';
    }

    ?>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        select {
            width: 100%;
            padding: 16px 20px;
            border: none;
            border-radius: 4px;
            background-color: #f4f4f4;
        }

        .formdata {
            width: 100%;
            padding: 16px 20px;
            border: none;
            border-radius: 4px;
            background-color: #f4f4f4;
            outline-color: #ffffff !important;
        }

        .formdata:focus {
            background-color: #ffffff !important;
            border: 1px solid #00AEEF !important;
            outline-color: #ffffff !important;
        }

        label {
            color: #989898 !important'
        }

        label,
        input {
            transition: all 0.2s;
            touch-action: manipulation;
        }

        [type="checkbox"]
        {
            vertical-align:middle;
            outline-color: #ffffff !important;
            cursor: pointer;
        }

        .title {
            font-weight: 600 !important;
            text-align: center;
            color: black;
            border-bottom: 1px solid #f2f2f2;
            margin-bottom: 20px !important;
            margin-top: 20px !important;
            font: 24px / 33px "Helvetica Neue", Helvetica, Arial, sans-serif;
            width: 60%;
            margin: auto;
        }

        .checkboxes {
            font-size: 12px;
        }

        .submit {
            width: 100%;
            padding: 14px;
            text-align: center;
            background-color: #00AEEF;
            border-radius: 4px;
            color: white;
            font-size: 18px;
        }

        .bottom {
            border-bottom: 5px solid #00AEEF;
            padding-bottom: 2rem !important;
            border-radius: 10px;
        }

        .storefront-handheld-footer-bar ul li.search .site-search {
            bottom: -5em !important;
        }

        .storefront-handheld-footer-bar ul li.search.active .site-search {
            bottom: 100% !important;
        }

        .storefront-handheld-footer-bar ul.columns-3 li {
            width: 7em !important;
            outline-color: #ffffff !important;
        }
    </style>

    <body>
        <div class="title">
            REGISTRO DE DEPARTAMENTOS
        </div>
        <form method="post">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                    <label for="name">Nombre del departamento</label>
                    <input class="formdata" type="text" name="name" required>
                </div>
                <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                    <label for="description">Descripción</label>
                    <textarea class="formdata" name="description"></textarea>
                </div>
                <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 bottom">
                    <button class="submit" type="submit">Enviar</button>
                </div>
            </div>
        </form>
    </body>
    <?php
    return ob_get_clean();
}
add_shortcode('form_departaments', 'form_departaments');
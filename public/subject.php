<?php 

// crea el producto general y categoria "Subject" si no existe
add_action('init', function () {
    
    $meta_key   = 'master_subject_product';
    $meta_value = 'master_subject_product'; // Valor para identificarlo
    $cat_name   = 'Subject';

    // verifica si el producto existe
    $query = new WP_Query([
        'post_type'  => 'product',
        'meta_query' => [
            [
                'key'   => $meta_key,
                'value' => $meta_value,
            ]
        ]
    ]);
    if ( $query->have_posts() ) return; 

    // crea la categoria si no existe y obtiene su ID
    $category = term_exists($cat_name, 'product_cat');
    if ( !$category ) $category = wp_insert_term($cat_name, 'product_cat');
    
    $category_id = is_array($category) ? $category['term_id'] : $category;

    // crea el producto
    $product = new WC_Product_Simple();
    $product->set_name($cat_name);
    $product->set_status('publish');
    $product->set_catalog_visibility('hidden'); 
    $product->set_price('0');
    $product->set_regular_price('0');
    $product->set_category_ids([$category_id]);
    $product->update_meta_data($meta_key, $meta_value);
    $product->save();

});

// obtiene el ID del producto general de los subjects
function get_master_subject_product_id() {
    $query = new WP_Query([
        'post_type'  => 'product',
        'fields'     => 'ids',
        'meta_query' => [['key' => 'master_subject_product', 'value' => 'master_subject_product']]
    ]);
    return $query->have_posts() ? $query->posts[0] : 0;
}

add_filter('woocommerce_add_to_cart_handler', function($handler, $product_id) {

    if ( isset($_REQUEST['subject_id']) ) {
        wc_empty_cart();
    }
    return $handler;
}, 10, 2);

// Inserta datos personalizados en el carrito
add_filter('woocommerce_add_cart_item_data', function($cart_item_data, $product_id, $variation_id) {
    if (isset($_REQUEST['subject_id'])) {

        $subject_id = intval($_REQUEST['subject_id']);
        $subject = get_subject_details($subject_id);

        $price = floatval($subject->price);
        if( $price == '' ) {

            if( $subject->type == 'elective' ) {
                $default_price_electives = get_option('default_price_electives', 0);
                $price = floatval($default_price_electives);
            } else if( $subject->type == 'regular' ) {
                $default_price_regular_courses = get_option('default_price_regular_courses', 0);
                $price = floatval($default_price_regular_courses);
            }
        }
        
        $cart_item_data['subject_id'] = $subject->id;
        $cart_item_data['custom_name_subject'] = $subject->name;
        $cart_item_data['custom_price_subject'] = $price;
        $cart_item_data['custom_currency_subject'] = $subject->currency;

    }
    return $cart_item_data;
}, 10, 3);

// Modifica el nombre y precio en el carrito
add_action('woocommerce_before_calculate_totals', function($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    foreach ( $cart->get_cart() as $cart_item ) {

        if ( isset($cart_item['custom_name_subject']) ) 
            $cart_item['data']->set_name($cart_item['custom_name_subject']);
        
        if ( isset($cart_item['custom_price_subject']) )
            $cart_item['data']->set_price($cart_item['custom_price_subject']);

        if( isset($cart_item['custom_currency_subject']) ) {
            add_filter('woocommerce_currency', function() use ($cart_item) {
                return $cart_item['custom_currency_subject'];
            });
        }
    }
}, 10, 1);

// Pasa los datos del carrito a la orden final
add_action('woocommerce_checkout_create_order_line_item', function($item, $cart_item_key, $values, $order) {
    
    if ( isset($values['subject_id']) ) {
        $order->update_meta_data('subject_id', sanitize_text_field($values['subject_id']));
        $item->add_meta_data('subject_id', $values['subject_id']);
    }

}, 10, 4);

/* //  llama el shortcode de comprar materias reprobadas
add_action('woocommerce_account_dashboard', function () {

    echo do_shortcode('[buy_failed_subjects]');
}, 1, 1); */




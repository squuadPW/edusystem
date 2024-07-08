<?php
// Update cart price based on the selected radio input value
add_action('wp_ajax_update_cart', 'update_cart_price');
add_action('wp_ajax_nopriv_update_cart', 'update_cart_price');

function update_cart_price() {
  // Get the selected radio input value
  $selectedValue = $_POST['option'];

  // Update the cart price based on the selected value
  // You can use WooCommerce functions to update the cart price
  // For example:
  $cart = WC()->cart;
  $cart->set_total($selectedValue == 'option1' ? 10 : ($selectedValue == 'option2' ? 20 : 30));

  // Return the updated cart totals
  ob_start();
  wc_cart_totals();
  $cart_totals = ob_get_clean();
  echo $cart_totals;
  die();
}
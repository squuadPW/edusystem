<?php
  global $woocommerce;
  $cart = $woocommerce->cart->get_cart();
  // AWS ID
   $id = 63;

  // DREAMHOST ID
  // $id = 484;

  // LOCAL JOSE MORA
  //$id = 484;

  $filtered_products = array_filter($cart, function($product) use($id) {
      return $product['product_id'] != $id;
  });

  foreach ($filtered_products as $key => $product) {
    $product_id = $product['product_id'];
    $product = wc_get_product($product_id);
  }

  // Check if the product is a variable product
  if (isset($product) && $product->is_type('variable')) {
    ?>

    <div >
      <div style="margin-bottom: 10px !important;">
        <label class="fee-container"><strong>Registration fee $299.</strong> <span>(You can pay it before starting classes in your account)</span>
          <input name="fee" type="checkbox" checked="checked">
          <span class="checkmark"></span>
        </label>
      </div>
    </div>

    <div class="text-center">
      <label>Apply your scholarship to get the discount</label>
      <div id="button-schoolship"></div>
    </div>

      <div class="radio-group text-center">
        <label class="m-5">Program Payments</label>
        <div class="radio-group">

          <?php
            global $woocommerce;
            $cart = $woocommerce->cart->get_cart();

            $filtered_products = array_filter($cart, function($product) use($id) {
                return $product['product_id'] != $id;
            });
            foreach ($filtered_products as $key => $product) {
              $product_id = $product['product_id'];
              $product = wc_get_product($product_id);
              $variations = $product->get_available_variations();
              foreach ($variations as $key => $variation) {
                $cuotes = get_post_meta($variation['variation_id'], 'num_cuotes_text', true );
                ?>
                <div class="radio-input">
                    <input <?php echo $key === 0 ? 'checked' : '' ?> class="form-check-input" type="radio" id="<?php echo $variation['variation_id']; ?>" name="option" value="<?php echo $variation['attributes']['attribute_pagos']; ?>">
                    <label class="form-check-label" for="<?php echo $variation['variation_id']; ?>">
                      <?php echo $cuotes . ($key === 0 ? ' Payment' : ' Payments'); ?> 
                    </label>
                </div>
                <?php
              }
            }
          ?>

        </div>
      </div>
    <?php
  }
?>

<div id="table-payment">
 
</div>

<input type="hidden" name="submit" value="Apply Scholarship">
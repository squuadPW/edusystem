<?php
  global $woocommerce;
  $cart = $woocommerce->cart;
  foreach ($cart->get_cart() as $key => $product) {
    $product_id = $product['product_id'];
    $product = wc_get_product($product_id);
  }

  // Check if the product is a variable product
  if ($product->is_type('variable')) {
    ?>
    
    <div class="text-center">
      <label>Apply your scholarship to get the discount</label>
      <div id="button-schoolship"></div>
    </div>

      <div class="radio-group text-center">
        <label class="m-5">Program Payments</label>
        <div class="radio-group">

          <?php
            global $woocommerce;
            $cart = $woocommerce->cart;
            foreach ($cart->get_cart() as $key => $product) {
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
<?php
  global $woocommerce;
  $cart = $woocommerce->cart->get_cart();
  $id = AES_FEE_INSCRIPTION;

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
      <?php
        $product_fee = wc_get_product(AES_FEE_INSCRIPTION);
        $product_price = $product_fee->get_price();
      ?>
        <?php if($product_price < 299) { ?>
            <label class="fee-container"><strong>Registration fee <span style="text-decoration: line-through;"><?php echo wc_price(299) ?></span></strong><span> with a 50% discount, you pay: <strong><?php echo wc_price($product_price) ?></strong> <br> (You can pay it before starting classes in your account)</span>
          <?php } else { ?>
            <label class="fee-container"><strong>Registration fee <?php echo wc_price($product_price) ?></strong> <br><span>(You can pay it before starting classes in your account)</span>
            <?php } ?>
          <input name="fee" type="checkbox" checked="checked">
          <span class="checkmark"></span>
        </label>
      </div>
    </div>

    <div class="text-center" style="padding: 18px 0px;">
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
                    <input <?php echo $key === 0 ? 'checked' : '' ?> class="form-check-input" type="radio" id="<?php echo $variation['variation_id']; ?>" name="option" value="<?php echo $variation['attributes']['attribute_payments']; ?>">
                    <label class="form-check-label" for="<?php echo $variation['variation_id']; ?>">
                      <?php echo $cuotes . ($key === 0 ? ' Payment<br>(Unique)' : ' Payments <br> (' . $variation['attributes']['attribute_payments'] . ')'); ?> 
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
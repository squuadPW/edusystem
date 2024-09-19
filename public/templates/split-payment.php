<?php
$order_id = null;
if (function_exists('wc_get_order')) {
    $order_id = wc_get_order(get_query_var('order-pay'));
    if (empty($order_id)) {
        $order_id = wc_get_order(get_query_var('order-received'));
    }
}
$order_id ? $order_id->get_id() : null;
$order = wc_get_order($order_id); // Obtiene la orden actual
$split_payment_metadata = '';
if ($order) {
    $split_payment_metadata = $order->get_meta('split_payment'); // Obtiene el metadato 'split_payment'
}

// Establece el valor del checkbox según el metadato
$aes_split_payment_checked = ($split_payment_metadata !== '') ? 'checked disabled' : '';
$style = ($split_payment_metadata !== '') ? 'block' : 'none';
?>

<div style="margin: 10px">
    <!-- The checkbox -->
    <div style="margin-bottom: 30px; margin-top: -10px">
        <input type="checkbox" id="aes_split_payment" name="aes_split_payment" onchange="showInput(this.checked)" <?php echo $aes_split_payment_checked; ?>> Use split payment
    </div>

    <!-- The input and button container -->
    <div class="input-container" style="display: <?php echo $style ?>">
        <!-- The input for entering the amount -->
        <div>
            <label for="aes_amount_split">Amount:</label><br>
            <input type="number" id="aes_amount_split" name="aes_amount_split" style="width: 100%" ><br><br>
        </div>

        <!-- The button to generate parts -->
        <!-- <div style="text-align: center; display: flex; justify-content: space-evenly;">
            <div style="width: 45%; border-top: 1px solid gray;"></div><span style="margin-top: -10px;">Or</span><div style="width: 45%; border-top: 1px solid gray;"></div>
         </div>
        <button id="generate-button" class="button button-primary" style="margin: 10px 0px !important;">Generate payment splits</button> -->
    </div>
</div>

<script>
    // Function to show or hide the input and button based on the checkbox state
    function showInput(checked) {
        if (checked) {
            document.querySelector('.input-container').style.display = 'block';
        } else {
            document.querySelector('.input-container').style.display = 'none';
        }
    }
</script>
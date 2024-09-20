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
            <div style="display: flex">
                <div style="width: 80%">
                    <input type="number" id="aes_amount_split" name="aes_amount_split" style="width: 100%" >
                </div>
                <div style="width: 20%; text-align: center">
                    <button type="button" onclick="loadTotalPayment()">Total payment</button>
                </div>
            </div><br><br>
            <input type="hidden" id="aes_amount_split_fee" name="aes_amount_split_fee" style="width: 100%" ><br><br>
            <span id="text_fee">el monto se sumara el fee del metodo de pago: <span id="total_entered"></span></span>
        </div>

        <!-- The button to generate parts -->
        <!-- <div style="text-align: center; display: flex; justify-content: space-evenly;">
            <div style="width: 45%; border-top: 1px solid gray;"></div><span style="margin-top: -10px;">Or</span><div style="width: 45%; border-top: 1px solid gray;"></div>
         </div>
        <button id="generate-button" class="button button-primary" style="margin: 10px 0px !important;">Generate payment splits</button> -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>
    let current_fee = 0;
    let current_payment_method_text = 0;
    document.getElementById('total_entered').innerText = `Loading...`;

    setTimeout(() => {
        let current_payment_method = $('input[name="payment_method"]').val();
        current_payment_method_text = current_payment_method;
        loadInfoFee(current_payment_method)
    }, 1000);

    $(document).on('change', 'input[name="payment_method"]', function() {
        document.querySelector('input[name="aes_split_payment"]').disabled = true;
        document.querySelector('input[name="aes_amount_split"]').value = 0;
        document.getElementById('total_entered').innerText = `Loading...`;
        current_payment_method_text = $(this).val();

        setTimeout(() => {
            loadInfoFee($(this).val())
        }, 1000);
    });

    function loadInfoFee(pay) {
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                'action': 'load_cart_for_split',
                'option': pay
            },
            success: function(response) {
                loadFee(response.fee); // Output: The subtotal of the cart
            }
        });
    }

    function loadTotalPayment() {
        // document.querySelector('input[name="aes_amount_split"]').readonly = true;
        // document.getElementById('text_fee').style.display = 'none';

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                'action': 'load_total_amount_for_split',
                'pay': current_payment_method_text
            },
            success: function(response) {
                document.querySelector('input[name="aes_amount_split"]').value = response.pending;
            }
        });
    }

    // Function to show or hide the input and button based on the checkbox state
    function showInput(checked) {
        if (checked) {
            document.querySelector('.input-container').style.display = 'block';
        } else {
            document.querySelector('.input-container').style.display = 'none';
        }
    }

    loadFee();
    function loadFee(amount = null) {
        if (amount != null) {
            current_fee = amount;
            document.getElementById('total_entered').innerText = `$${amount}`;
            document.querySelector('input[name="aes_split_payment"]').disabled = false;
        } else {
            const feeTr = document.querySelector('.fee');
            if (feeTr) {
                const feeTd = feeTr.querySelector('td');
                let text = feeTd.textContent;
                text = text.split('$');

                current_fee = parseFloat(text[1]);
                document.getElementById('total_entered').innerText = `$${parseFloat(text[1])}`;
                document.querySelector('input[name="aes_split_payment"]').disabled = false;
            }
        }

        document.querySelector('input[name="aes_amount_split_fee"]').value = current_fee;
    }

    document.getElementById('aes_amount_split').addEventListener('input',(e) => {
        document.getElementById('total_entered').innerText = `$${parseFloat(e.target.value) + parseFloat(current_fee)}`
    });
</script>
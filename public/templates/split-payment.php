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
$style_button_total_payment = 'none';
$split_payment_page = 0;
if ($order) {
    $style_button_total_payment = 'block';
    $split_payment_page = 1;
    $split_payment_metadata = $order->get_meta('split_payment'); // Obtiene el metadato 'split_payment'
}

// Establece el valor del checkbox según el metadato
$aes_split_payment_checked = ($split_payment_metadata !== '') ? 'checked disabled' : '';
$style = ($split_payment_metadata !== '') ? 'block' : 'none';
$style_checkbox = ($split_payment_metadata !== '') ? 'none' : 'block';
?>

<div>
    <div style="padding: 22.652px; text-align: center; background-color: #f5f5f5; font-weight: 600; margin-top: -18px;">
        Payment methods
    </div>

    <?php 
    $style_checkbox_split = "";
    if($order && $order->get_meta('cuote_payment')) {
        $style_checkbox_split = "display: none !important";
    } ?>

    <div style="padding: 18px; <?php echo $style_checkbox_split; ?>">
        <!-- The checkbox -->
        <div style="padding: 10px; font-weight: 600; display: <?php echo $style_checkbox ?>">
            <label class="fee-container" style="margin-bottom: 0px !important">
                <strong>Split payment</strong>
                <input type="checkbox" id="aes_split_payment" name="aes_split_payment"
                    onchange="showInput(this.checked)" <?php echo $aes_split_payment_checked; ?>>
                <span class="checkmark"></span>
            </label>
        </div>

        <!-- The input and button container -->
        <div class="input-container" style="margin-top: 10px; display: <?php echo $style ?>">
            <!-- The input for entering the amount -->
            <div>
                <p style="font-style: italic; font-size: 14px">A split payment is a payment method that allows you to
                    divide a single transaction into multiple payments. This means that instead of paying the full
                    amount upfront, you can split it into two or more installments, making it more manageable and
                    convenient for your budget.</p>
                <div style="margin-top: 10px;">
                    <div>
                        <label for="aes_amount_split">Amount to pay</label>
                        <div style="position: relative; display: inline-block; width: 100%;">
                            <input type="text" id="aes_amount_split_visual" name="aes_amount_split_visual"
                                style="width: 100%; padding-right: <?php echo $style_button_total_payment == 'none' ? '10px' : '150px' ?>; box-sizing: border-box; -webkit-appearance: none; -moz-appearance: textfield;"
                                class="money">
                            <input type="hidden" id="aes_amount_split" name="aes_amount_split">
                            <button id="total_payment_button" type="button" class="submit"
                                style="font-size: 14px; padding: 10px 10px !important; border-radius: 9px !important; position: absolute; top: 50%; right: 8px; height: 36px; transform: translateY(-50%); width: auto; display: <?php echo $style_button_total_payment ?>"
                                onclick="clickLoadInfoFee()">Pending amount</button>
                        </div>
                        <label for="aes_amount_split" style="font-size: 14px">Payment method commission <strong
                                id="payment_method_comission"></strong> <br> Total paid: <strong id="total_entered"
                                style="color: green;"></strong></label><br>
                    </div>
                </div>
                <input type="hidden" id="aes_amount_split_fee" name="aes_amount_split_fee" style="width: 100%">
                <input type="hidden" id="aes_payment_page" name="aes_payment_page"
                    value="<?php echo $split_payment_page ?>" style="width: 100%">
            </div>

            <!-- The button to generate parts -->
            <!-- <div style="text-align: center; display: flex; justify-content: space-evenly;">
            <div style="width: 45%; border-top: 1px solid gray;"></div><span style="margin-top: -10px;">Or</span><div style="width: 45%; border-top: 1px solid gray;"></div>
        </div>
        <button id="generate-button" class="button button-primary" style="margin: 10px 0px !important;">Generate payment splits</button> -->
        </div>
    </div>
</div>

<?php
if ($order) {
    global $current_user;
    $payments = json_decode($order->get_meta('split_method'));
    if ($order->get_meta('pending_payment') && $order->get_meta('pending_payment') > 0) { ?>
        <div id='modalNextPayment' class='modal' style="display: block">
            <div class='modal-content'>
                <div class="modal-header">
                    <h3 style="font-size:20px;">Payment received</h3>
                    <span class="modal-close"><span class="dashicons dashicons-no-alt"></span></span>
                </div>
                <div class="modal-body" style="margin: 10px; padding: 0px">
                    <p>Dear <?php echo $current_user->first_name . ' ' . $current_user->last_name ?>,</p>

                    <p>We are pleased to inform you that we have successfully received your last payment
                        <?php echo $payments[count($payments) - 1]->method ?>, in the amount of
                        <strong><?php echo wc_price($payments[count($payments) - 1]->amount) ?></strong>.
                    </p>

                    <p>Please proceed to the next payment step to complete your transaction.</p>

                    <p>Best regards, American Elite School</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="continue-payment"
                        class="button button-primary"><?= __('Continue', 'aes'); ?></button>
                </div>
            </div>
        </div>
    <?php }
}
?>


<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js"
    integrity="sha512-0XDfGxFliYJPFrideYOoxdgNIvrwGTLnmK20xZbCAvPfLGQMzHUsaqZK8ZoH+luXGRxTrS46+Aq400nCnAT0/w=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>

    $(document).ready(function () {
        $('.money').mask('#,##0.00', { reverse: true });
    });

    let button_continue = document.getElementById('continue-payment');
    if (button_continue) {
        button_continue.addEventListener('click', (e) => {
            document.getElementById('modalNextPayment').style.display = "none";
        });
    }

    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click', (e) => {
            document.getElementById('modalNextPayment').style.display = "none";
        });
    });

    let current_fee = 0;
    let current_payment_method_text = '';
    let pending_amount = 0;
    document.getElementById('total_entered').innerText = `Loading...`;
    document.getElementById('payment_method_comission').innerText = `Loading...`;

    setTimeout(() => {
        clickLoadInfoFee();
    }, 1000);

    function clickLoadInfoFee() {
        document.querySelector('input[name="aes_amount_split"]').value = 0;
        document.querySelector('input[name="aes_amount_split_visual"]').value = 0;
        document.getElementById('total_entered').innerText = `Loading...`;
        document.getElementById('payment_method_comission').innerText = `Loading...`;
        document.getElementById('total_payment_button').disabled = true;

        current_payment_method_text = current_payment_method_text ? current_payment_method_text : $('input[name="payment_method"]').val();
        loadInfoFee(current_payment_method_text);
    }

    $(document).on('change', 'input[name="payment_method"]', function (e) {
        changePaymentMethod($(this).val());
    });

    function changePaymentMethod(value) {
        document.querySelector('input[name="aes_amount_split"]').value = 0;
        document.querySelector('input[name="aes_amount_split_visual"]').value = 0;
        document.getElementById('total_entered').innerText = `Loading...`;
        document.getElementById('payment_method_comission').innerText = `Loading...`;
        document.getElementById('total_payment_button').disabled = true;

        current_payment_method_text = value;

        setTimeout(() => {
            loadInfoFee(value)
        }, 500);
    }

    function loadInfoFee(pay) {
        aes_payment_page = document.getElementById('aes_payment_page').value;
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                'action': 'load_cart_for_split',
                'option': pay,
                'payment_page': aes_payment_page
            },
            success: function (response) {
                loadFee(response.fee); // Output: The subtotal of the cart

                pending_amount = response.pending;
                document.querySelector('input[name="aes_amount_split"]').value = pending_amount;

                const $element = $('.money');
                if (pending_amount < 1000) {
                    $element.val(0); // apply the phone number mask
                    $element.mask('#,##0.00'); // apply the phone number mask
                }

                $element.val(pending_amount); // set the value to 1234567890
                $element.mask('#,##0.00'); // apply the phone number mask

                document.getElementById('place_order').disabled = false;
                let value = document.getElementById('aes_amount_split').value ? document.getElementById('aes_amount_split').value : 0;
                let amount_calculated = (parseFloat(value) + parseFloat(current_fee)).toFixed(2);
                document.getElementById('total_entered').innerText = parseFloat(value) > 0 ? `$${amount_calculated.toLocaleString('en-US')}` : 0.00;
                document.getElementById('payment_method_comission').innerText = parseFloat(value) > 0 ? `($${current_fee})` : `$0.00`;
                document.getElementById('total_payment_button').disabled = false;

                loadTableTotalsFee(response.fee);
            }
        });
    }

    function loadTableTotalsFee(fee, from_input = 0) {
        fee = parseFloat(fee);
        aes_payment_page = document.getElementById('aes_payment_page').value;
        if (aes_payment_page == 1) {
            $('table.shop_table tfoot tr.fee-row').remove();

            // Crear un nuevo tr para la tarifa
            var newFeeRow = '<tr class="fee-row">' +
                '<th scope="row" colspan="2">Payment method fee:</th>' +
                '<td class="product-total"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>' + fee + '</bdi></span></td>' +
                '</tr>';

            // Agregar el nuevo tr al pie de la tabla en la segunda posición
            $('table.shop_table tfoot tr').eq(1).before(newFeeRow);

            // Verificar si hay exactamente dos tr después de la nueva fila fee-row
            var feeRowIndex = $('table.shop_table tfoot tr.fee-row').index();
            var rowsAfterFeeRow = $('table.shop_table tfoot tr').slice(feeRowIndex + 1); // Filas después de fee-row

            if (rowsAfterFeeRow.length === 2) {
                rowsAfterFeeRow.first().remove(); // Eliminar el primer tr después de fee-row
            }

            if (rowsAfterFeeRow.length === 5) {
                rowsAfterFeeRow.eq(1).remove(); // Eliminar la cuarta fila (índice 3)
            }

            let subtotal = 0;
            if (from_input) {
                subtotal = from_input;
            } else {
                subtotal = <?php echo $order ? $order->get_subtotal() : 0; ?>;
            }
            let new_total = subtotal + fee; // Calcula el nuevo total

            // Formatear el nuevo total como un precio
            let formattedTotal = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(new_total);

            let prices = document.querySelectorAll('.product-total > .woocommerce-Price-amount');
            let less = prices.length == 6 ? 3 : 1;
            prices[(prices.length - less)].innerText = formattedTotal;
        }
    }

    // Function to show or hide the input and button based on the checkbox state
    function showInput(checked) {
        if (checked) {
            document.querySelector('.input-container').style.display = 'block';
        } else {
            document.querySelector('.input-container').style.display = 'none';
        }

        changePaymentMethod(current_payment_method_text);
    }

    function loadFee(amount = null) {
        if (amount != null) {
            current_fee = amount;
            document.getElementById('total_entered').innerText = `$${amount}`;
        } else {
            const feeTr = document.querySelector('.fee');
            if (feeTr) {
                const feeTd = feeTr.querySelector('td');
                let text = feeTd.textContent;
                text = text.split('$');

                current_fee = parseFloat(text[1]);
                document.getElementById('total_entered').innerText = `$${parseFloat(text[1])}`;
            }
        }

        document.querySelector('input[name="aes_amount_split_fee"]').value = current_fee;
    }

    document.getElementById('aes_amount_split_visual').addEventListener('input', (e) => {
        $('.money').mask('#,##0.00', { reverse: true });
        let value_parsed = $('.money').cleanVal();
        let divisor = 1;
        if (value_parsed.length > 2) {
            divisor = 100;
        }
        loadAesAmountSplit(value_parsed / divisor);
    });

    function loadAesAmountSplit(value) {
        if (current_payment_method_text == 'woo_squuad_stripe') {
            const from_webinar = getCookie('from_webinar');
            let fee = 0;
            if (from_webinar !== null) {
                fee = 0; // 0% fee
            } else {
                fee = 4.5; // 4.5% fee
            }
            const cart_subtotal = value;
            current_fee = ((cart_subtotal / 100) * fee).toFixed(2);
        }
        current_fee = current_fee.toLocaleString('en-US');
        document.getElementById('payment_method_comission').innerText = parseFloat(value) > 0 ? `($${current_fee})` : `$0.00`;
        document.querySelector('input[name="aes_amount_split_fee"]').value = current_fee;
        document.querySelector('input[name="aes_amount_split"]').value = value;
        let amount_calculated = parseFloat(value) > 0 ? (parseFloat(value) + parseFloat(current_fee)).toFixed(2) : 0.00;
        document.getElementById('total_entered').innerText = `$${amount_calculated.toLocaleString('en-US')}`
        if (pending_amount > 0) {
            if (value > pending_amount) {
                document.getElementById('place_order').disabled = true;
            } else {
                document.getElementById('place_order').disabled = false;
            }
        } else {
            document.getElementById('place_order').disabled = false;
        }

        loadTableTotalsFee(current_fee, parseFloat(value));
    }

    function getCookie(name) {
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();
            if (cookie.startsWith(name + '=')) {
                return cookie.substring(name.length + 1);
            }
        }
        return null;
    }
</script>
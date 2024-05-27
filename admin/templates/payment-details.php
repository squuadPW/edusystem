<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Payment details','aes'); ?></h2>

    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>
    <div id="notice-payment-completed" style="display:none;" class="notice notice-info"><p><?= __('Payment Completed','aes'); ?></p></div>
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row" ><label for="input_id"><?= __('Payment ID','aes').':'; ?></label></th>
                                    <td>
                                        <?= '#'.$order->get_id(); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Date','aes').':'; ?></label></th>
                                    <td><?= $order->get_date_paid()->format('F j, Y g:i a') ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Partner Name','aes').':'; ?></label></th>
                                    <td><?= $order->get_billing_first_name().' '.$order->get_billing_last_name() ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Payment Total','aes').':'; ?></label></th>
                                    <td><?= get_woocommerce_currency_symbol().$order->get_total() ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Payment Method','aes').':'; ?></label></th>
                                    <td><?= $order->get_payment_method_title(); ?></td>
                                </tr>
                                <?php if($order->get_meta('transaction_id')){ ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Transaction ID','aes').':'; ?></label></th>
                                        <td><?= $order->get_meta('transaction_id'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if($order->get_status() != 'completed'){ ?>
                            <div style="display:flex;flex-direction:row;width:100%;justify-content:end;">
                                <?php if(wp_is_mobile()){ ?>
                                    <button data-id="<?= $order->get_id(); ?>" id="approved_payment" style="width:100%;" class="button button-primary"><?= __('Approved Payment','aes'); ?></button>
                                <?php }else{ ?>
                                    <button data-id="<?= $order->get_id(); ?>" id="approved_payment" class="button button-primary"><?= __('Approved Payment','aes'); ?></button>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
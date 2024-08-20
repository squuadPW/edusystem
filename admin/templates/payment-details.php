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
                                    <td><?= $order->get_date_created()->format('F j, Y g:i a') ?></td>
                                </tr>
                                <?php if(!in_array('institutes',$roles)): ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Parent Name','aes').':'; ?></label></th>
                                        <td><?= $order->get_billing_first_name().' '.$order->get_billing_last_name() ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if(!in_array('institutes',$roles) && $order->get_meta('student_data')): ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Student Name','aes').':'; ?></label></th>
                                        <td><?= $order->get_meta('student_data')['name_student'] . ' ' .  $order->get_meta('student_data')['middle_name_student'] . ' ' .  $order->get_meta('student_data')['last_name_student'] . ' ' .  $order->get_meta('student_data')['middle_last_name_student'] ?></td>

                                    </tr>
                                <?php endif; ?>
                                <?php if(!in_array('institutes',$roles) && !in_array('alliance',$roles)): ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Payment Total','aes').':'; ?></label></th>
                                        <td><?= get_woocommerce_currency_symbol().$order->get_total() ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if(in_array('institute',$roles) && $order->get_meta('institute_fee')): ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Fee','aes').':'; ?></label></th>
                                        <td><?= get_woocommerce_currency_symbol().number_format(floatval($order->get_meta('institute_fee')),2,'.',','); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <?php if(!in_array('alliance',$roles) && $order->get_meta('institute_fee')): ?>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Institute Fee','aes').':'; ?></label></th>
                                            <td><?= get_woocommerce_currency_symbol().number_format(floatval($order->get_meta('institute_fee')),2,'.',','); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if(in_array('alliance',$roles) && $order->get_meta('institute_fee')): ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Fee','aes').':'; ?></label></th>
                                        <td><?= get_woocommerce_currency_symbol().number_format(floatval($order->get_meta('alliance_fee')),2,'.',','); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <?php if(!in_array('institutes',$roles) && $order->get_meta('institute_fee')): ?>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Alliance Fee','aes').':'; ?></label></th>
                                            <td><?= get_woocommerce_currency_symbol().number_format(floatval($order->get_meta('alliance_fee')),2,'.',','); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Payment Method selected','aes').':'; ?></label></th>
                                    <td><?= $order->get_payment_method_title(); ?></td>
                                </tr>
                                <?php if($order->get_meta('_stripe_intent_id')){ ?>
                                    <?php if(!in_array('institute',$roles) && !in_array('alliance',$roles)): ?>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Transaction ID','aes').':'; ?></label></th>
                                            <td><?= $order->get_meta('_stripe_intent_id'); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php } ?>
                                <?php if($order->get_meta('payment_method')){ ?>
                                    <?php if(!in_array('institute',$roles) && !in_array('alliance',$roles)): ?>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Payment method used','aes').':'; ?></label></th>
                                            <td><?= $order->get_meta('payment_method'); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php } ?>
                                <?php if($order->get_meta('transaction_id')){ ?>
                                    <?php if(!in_array('institute',$roles) && !in_array('alliance',$roles)): ?>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Transaction ID','aes').':'; ?></label></th>
                                            <td><?= $order->get_meta('transaction_id'); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if(!in_array('institutes',$roles) && !in_array('alliance',$roles)): ?>

                            <table id="table-products" class="wp-list-table widefat fixed posts striped" style="margin-top:20px;">
                            <thead>
                                <tr>
                                    <th scope="col" class="manage-column column-primary column-title"><?= __('Program','aes') ?></th>
                                    <th scope="col" class="manage-column column-price"><?= __('Total','aes') ?></th>
                                </tr>
                            </thead>
                            <tbody id="table-documents">
                                <?php foreach($order->get_items() as $item){ ?>
                                    <tr>
                                        <td class="column-primary">
                                            <?= $item->get_name(); ?>
                                        </td>
                                        <td data-colname="<?= __('Total','aes'); ?>">
                                            <?= get_woocommerce_currency_symbol().number_format($item->get_total(),2,'.',','); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tobdy>
                            </table>

                            <?php if($order->get_status() != 'completed'){ ?>
                                <div style="margin-top:10px;display:flex;flex-direction:row;width:100%;justify-content:end;">
                                    <?php if(wp_is_mobile()){ ?>
                                        <button data-message="<?= __('Do you want to approve this payment?','aes'); ?>" data-title="<?= __('Approve','aes'); ?>" data-id="<?= $order->get_id(); ?>" id="approved_payment" style="width:100%;" class="button button-primary"><?= __('Approve','aes'); ?></button>
                                    <?php }else{ ?>
                                        <button data-message="<?= __('Do you want to approve this payment?','aes'); ?>" data-title="<?= __('Approve','aes'); ?>" data-id="<?= $order->get_id(); ?>" id="approved_payment" class="button button-primary"><?= __('Approve','aes'); ?></button>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    include(plugin_dir_path(__FILE__).'modal-status-payment.php');
?>
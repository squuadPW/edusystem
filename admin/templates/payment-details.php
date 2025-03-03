<style>
    .form-table th {
        width: auto !important;
    }
</style>

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
                                <?php if($order->get_meta('from_webinar')) { ?>
                                    <h4 style="text-align: center; font-weight: 600; font-style: italic; color: #2271b1; font-size: 18px;">From webinar</h4>
                                <?php } ?>
                                <?php if($order->get_meta('is_scholarship')) { ?>
                                    <h4 style="text-align: center; font-weight: 600; font-style: italic; color: #1f8605; font-size: 18px;">Scholarship</h4>
                                <?php } ?>
                                <tr>
                                    <th scope="row" ><label for="input_id"><?= __('Status','aes').':'; ?></label></th>
                                    <td>
                                        <?= strtoupper($order->get_status()); ?>
                                    </td>
                                </tr>
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
                                <?php if($order->get_meta('fee_order_pay') && $order->get_meta('fee_order_pay') > 0): ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Fee payment method','aes'); ?></label></th>
                                        <td><?= wc_price($order->get_meta('fee_order_pay')); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if(!in_array('institutes',$roles) && !in_array('alliance',$roles)): ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Payment Total','aes').':'; ?></label></th>
                                        <td><?= wc_price($order->get_total()) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if(in_array('institute',$roles) && $order->get_meta('institute_fee')): ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Fee','aes').':'; ?></label></th>
                                        <td><?= wc_price($order->get_meta('institute_fee')); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <?php if(!in_array('alliance',$roles) && $order->get_meta('institute_fee')): ?>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Institute Fee','aes').':'; ?></label></th>
                                            <td><?= wc_price($order->get_meta('institute_fee')); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if(in_array('alliance',$roles) && $order->get_meta('institute_fee')): ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Fee','aes').':'; ?></label></th>
                                        <td><?= wc_price($order->get_meta('alliance_fee')); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <?php if(!in_array('institutes',$roles) && $order->get_meta('institute_fee')): ?>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Alliance Fee','aes').':'; ?></label></th>
                                            <td><?= wc_price($order->get_meta('alliance_fee')); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if($order->get_meta('split_payment') && $order->get_meta('split_payment') == 1) { ?>
                                    <?php 
                                    $payments = json_decode($order->get_meta('split_method'));
                                    foreach ($payments as $key => $pay) {                                    
                                    ?>
                                        <tr style="border-top: 1px dashed gray;">
                                            <th style="text-align: center;"><?= __('Payment Method N°','aes') . ($key + 1); ?></th>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Payment Method','aes'); ?></label></th>
                                            <td><?= $pay->method; ?></td>
                                        </tr>
                                        <?php if($pay->payment_method) { ?>
                                            <tr>
                                                <th scope="row"><label for="input_id"><?= __('Method','aes'); ?></label></th>
                                                <td><?= $pay->payment_method; ?></td>
                                            </tr>
                                        <?php } ?>
                                        <?php if($pay->transaction_id) { ?>
                                            <tr>
                                                <th scope="row"><label for="input_id"><?= __('Transaction ID','aes'); ?></label></th>
                                                <td><?= $pay->transaction_id; ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Gross amount','aes'); ?></label></th>
                                            <td><?= wc_price($pay->gross_total); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Net mount','aes'); ?></label></th>
                                            <td><?= wc_price($pay->amount); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Fee payment method','aes'); ?></label></th>
                                            <td><?= wc_price($pay->fee); ?></td>
                                        </tr>
                                        <tr style="border-bottom: 1px dashed gray;">
                                            <th scope="row"><label for="input_id"><?= __('Status','aes'); ?></label></th>
                                            <td><?= $pay->status == 'completed' || $pay->status == 'complete' ? 'Completed' : ($pay->status == 'refunded' ? 'Refunded' : 'On hold'); ?></td>
                                        </tr>
                                    <?php } ?>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Total paid net','aes'); ?></label></th>
                                            <td><?= wc_price($order->get_meta('total_paid')); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Total paid gross','aes'); ?></label></th>
                                            <td><?= wc_price($order->get_meta('total_paid_gross')); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="input_id"><?= __('Pending payment','aes'); ?></label></th>
                                            <td><?= wc_price($order->get_meta('pending_payment')); ?></td>
                                        </tr>
                                <?php } else { ?>
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
                                <?php } ?>
                                <?php
                                    $student = get_student_detail_partner($order->get_customer_id());
                                ?>
                                <tr>
                                    <td colspan="2" style="text-align: center;">
                                        <a target="_blank" href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=') . $student->email ?>"
                                            class="button button-outline-primary">
                                            <?= __('Manage payments', 'aes'); ?>
                                        </a>
                                    </td>
                                </tr>
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
                                            <?= wc_price($item->get_total()); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tobdy>
                            </table>
                            <?php global $current_user;
                                $roles = $current_user->roles;
                                if(!in_array('webinar-aliance', $roles)){?>
                                <div style="margin-top:10px;display:flex;flex-direction:row;width:100%;justify-content:end;">
                                    <?php if($order->get_status() == 'on-hold'){ ?>
                                        <div style="margin-right: 10px">
                                            <?php if(wp_is_mobile()){ ?>
                                                <button data-message="<?= __('Do you want to decline this payment?','aes'); ?>" data-title="<?= __('Decline','aes'); ?>" data-id="<?= $order->get_id(); ?>" id="decline_payment" style="width:100%;" class="button button-danger"><?= __('Decline','aes'); ?></button>
                                            <?php }else{ ?>
                                                <button data-message="<?= __('Do you want to decline this payment?','aes'); ?>" data-title="<?= __('Decline','aes'); ?>" data-id="<?= $order->get_id(); ?>" id="decline_payment" class="button button-danger"><?= __('Decline','aes'); ?></button>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <?php if(($order->get_status() == 'on-hold' || $order->get_status() == 'pending') && ($order->get_meta('split_payment') && $order->get_meta('split_payment') == 1) && ($order->get_meta('pending_payment') && $order->get_meta('pending_payment') > 0)){ ?>
                                        <div style="margin-right: 10px">
                                            <?php if(wp_is_mobile()){ ?>
                                                <button data-total="<?= $order->get_meta('pending_payment') ?>" data-message="<?= __('Do you want to set the date of the next payment?','aes'); ?>" data-title="<?= __('Generate next agreed payment','aes'); ?>" data-id="<?= $order->get_id(); ?>" id="generate_order_split" style="width:100%;" class="button button-primary"><?= __('Payment agreement','aes'); ?></button>
                                            <?php }else{ ?>
                                                <button data-total="<?= $order->get_meta('pending_payment') ?>" data-message="<?= __('Do you want to set the date of the next payment?','aes'); ?>" data-title="<?= __('Generate next agreed payment','aes'); ?>" data-id="<?= $order->get_id(); ?>" id="generate_order_split" class="button button-primary"><?= __('Payment agreement','aes'); ?></button>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>

                                    <?php if($order->get_status() == 'on-hold'){ ?>
                                        <div>
                                            <?php if(wp_is_mobile()){ ?>
                                                <button data-message="<?= __('Do you want to approve this payment?','aes'); ?>" data-title="<?= __('Approve','aes'); ?>" data-id="<?= $order->get_id(); ?>" id="approved_payment" style="width:100%;" class="button button-success"><?= __('Approve','aes'); ?></button>
                                            <?php }else{ ?>
                                                <button data-message="<?= __('Do you want to approve this payment?','aes'); ?>" data-title="<?= __('Approve','aes'); ?>" data-id="<?= $order->get_id(); ?>" id="approved_payment" class="button button-success"><?= __('Approve','aes'); ?></button>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>

                                    <?php if($order->get_status() == 'pending'){ ?>
                                        <div>
                                            <?php if(wp_is_mobile()){ ?>
                                                <button data-message="<?= __('Do you want to approve this payment?','aes'); ?>" data-title="<?= __('Approve','aes'); ?>" data-id="<?= $order->get_id(); ?>" id="approved_payment" style="width:100%;" class="button button-success"><?= __('Approve','aes'); ?></button>
                                            <?php }else{ ?>
                                                <button data-message="<?= __('Do you want to approve this payment?','aes'); ?>" data-title="<?= __('Approve','aes'); ?>" data-id="<?= $order->get_id(); ?>" id="approved_payment" class="button button-success"><?= __('Approve','aes'); ?></button>
                                            <?php } ?>
                                        </div>
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
    $split_payment = $order->get_meta('split_payment');
    $payments = json_decode($order->get_meta('split_method'));
    include(plugin_dir_path(__FILE__).'modal-status-payment.php');
    include(plugin_dir_path(__FILE__).'modal-generate-order.php');
?>
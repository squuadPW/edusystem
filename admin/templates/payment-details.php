<style>
    .form-table th {
        width: auto !important;
    }
</style>

<div class="wrap edusof-page-admin payment-details">
    <h2 style="margin-bottom:15px;"><?= __('Payment details','edusystem'); ?></h2>

    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>
    <div id="notice-payment-completed" style="display:none;" class="notice notice-info"><p><?= __('Payment Completed','edusystem'); ?></p></div>
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">

                <div class="container-card-admin" >
					<div class="card-content" >
						<h2 class="title"><?= __('Payment details','edusystem'); ?></h2>

                        <div class="seccion-vertical" >

                            <div>

                                <div class="seccion-card">
                                    <span class="dashicons dashicons-admin-post no-vertical seccion-icon" ></span>
                                    
                                    <p>
                                        <strong><?=__('ID','edusystem')?>:</strong>
                                        #<?= $order->get_id() ?>
                                    </p>
                                </div>

                                <div class="seccion-card">
                                    <span class="dashicons dashicons-marker no-vertical seccion-icon" ></span>
                                    
                                    <p>
                                        <strong><?=__('Status','edusystem')?>:</strong>
                                        <?= wc_get_order_status_name($order->get_status()) ?>
                                    </p>
                                </div>

                                <div class="seccion-card">
                                    <span class="dashicons dashicons-calendar-alt no-vertical seccion-icon" ></span>
                                    
                                    <p>
                                        <strong><?=__('Date','edusystem')?>:</strong>
                                        <?=  wp_date('m/d/Y H:i:s', strtotime($order->get_date_created())) ?>
                                    </p>
                                </div>

                                <div class="seccion-card">
                                    <span class="dashicons dashicons-money-alt no-vertical seccion-icon" ></span>
                                    
                                    <p>
                                        <strong><?=__('Payment currency','edusystem')?>:</strong>
                                        <?= $order->get_currency(); ?>
                                    </p>
                                </div>

                            </div>

                            <div>

                                <?php if(!in_array('institutes',$roles) && $student): ?>
                                    <div class="seccion-card">
                                        <span class="dashicons dashicons-admin-users no-vertical seccion-icon" ></span>
                                        
                                        <p>
                                            <strong><?=__('Student','edusystem')?>:</strong>
                                            <?= student_names_lastnames_helper($student->id); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <?php
                                    $age = floor((time() - strtotime($student->birth_date)) / 31536000);
                                    $show_parent_info = 1;
                                    if ($age >= 18) {
                                        $show_parent_info = 0;
                                    }
                                ?>
                                <?php if(!in_array('institutes',$roles) && $show_parent_info): ?>
                                    <div class="seccion-card">
                                        <span class="dashicons dashicons-businessperson no-vertical seccion-icon" ></span>
                                        
                                        <p>
                                            <strong><?=__('Parent','edusystem')?>:</strong>
                                            <?= $order->get_billing_last_name().', '.$order->get_billing_first_name() ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <?php if( $order->get_meta('is_scholarship') || $order->get_meta('from_webinar') ): ?>
                                    <div class="seccion-card">
                                        <span class="dashicons dashicons-migrate no-vertical seccion-icon" ></span>
                                        
                                        <p>
                                            <strong><?=__('From','edusystem')?>:</strong>

                                            <?php if( $order->get_meta('from_webinar') ): ?>
                                                <span style="color: #2271b1;">From webinar</span>
                                            <?php endif ?>

                                            <?php if( $order->get_meta('is_scholarship') ): ?>
                                                <span style="color: #1f8605;">Scholarship</span>
                                            <?php endif ?>

                                        </p>
                                    </div>
                                <?php endif; ?>

                            </div>

                        </div>

					</div>
				</div>

                <div class="container-card-admin" >
					<div class="card-content" >

                        <div class="card-content-header" >

                            <h2 class="title"><?= __('Items','edusystem'); ?></h2>
                            
                            <?php if(!in_array('institutes',$roles) && !in_array('alliance',$roles)): ?>
                            <div class="container-button" >
                                <?php
                                    $student = get_student_detail_partner($order->get_customer_id());
                                ?>
    
                                <a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=') . $student->email ?>"
                                    class="button button-outline-primary">
                                    <?= __('Manage payments', 'edusystem'); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            
                        </div>

                        <?php if(!in_array('institutes',$roles) && !in_array('alliance',$roles)): ?>

                            <form method="POST" action="<?= admin_url('admin.php?page=add_admin_form_payments_content&action=update_price_items_order') ?>" >

                                <table id="table-products-payment" class="wp-list-table widefat fixed posts striped" style="margin-top:20px;">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="manage-column column-primary column-title"><?= __('Program','edusystem') ?></th>
                                            <th scope="col" class="manage-column column-price"><?= __('Regular price','edusystem') ?></th>
                                            <th scope="col" class="manage-column column-price"><?= __('Sale price','edusystem') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <input type="hidden" name="order_id" value="<?= $order->get_id(); ?>" />
                                       
                                        <?php foreach($order->get_items() as $item){ ?>
                                            <tr class="item-product-payment" >
                                                <td class="column-primary">
                                                    <?= $item->get_name(); ?>
                                                    <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                                                </td>
                                                <td data-colname="<?= __('Regular price','edusystem'); ?>">
                                                    <?= wc_price($item->get_subtotal()); ?>
                                                </td>
                                                <td data-colname="<?= __('Sale priceTotal','edusystem'); ?>">
                                                    <div class="total-price">
                                                        <?= wc_price($item->get_total()); ?>

                                                        <?php if( $order->status == 'pending' || $order->status == 'on-hold' ): ?>
                                                            <a onclick="active_edit_price_item();" >
                                                                <span class="dashicons dashicons-edit no-vertical seccion-icon" ></span>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="inputs-price hidden" >
                                                        <input type="number" class="input-text" name="items[<?= $item->get_id(); ?>][amount]" data-origin-price="<?= esc_attr($item->get_total() ); ?>" min="0" step="0.01" />
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        
                                        <?php foreach( $order->get_fees() as $item) { ?>
                                            <tr class="item-product-payment" >
                                                <td class="column-primary">
                                                    <?= $item->get_name(); ?>
                                                    <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                                                </td>
                                                <td data-colname="<?= __('Regular price','edusystem'); ?>">
                                                    <?= ''//wc_price($item->get_subtotal()) ?? '---'; ?>
                                                </td>
                                                <td data-colname="<?= __('Sale price total','edusystem'); ?>">
                                                    <div class="total-price">
                                                        <?= wc_price($item->get_total()); ?>

                                                        <?php if( $order->status == 'pending' || $order->status == 'on-hold' ): ?>
                                                            <a onclick="active_edit_price_item();" >
                                                                <span class="dashicons dashicons-edit no-vertical seccion-icon" ></span>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="inputs-price hidden" >
                                                        <?php 
                                                            $split_meta = $item->get_meta( 'split_payment_method' ) ? true : false;
                                                            $balance_discount = $item->get_meta( 'balance_discount' ) ? true : false;
                                                        ?>
                                                        
                                                        <input type="number" class="input-text" name="items[<?= $item->get_id(); ?>][amount]" 
                                                            step="0.01" <?= !$balance_discount ? 'min="0"' : 'max="0"' ?>
                                                            data-origin-price="<?= $item->get_total() ?>" 
                                                            <?= $split_meta ? 'data-fee-split-payment="true"' : '' ?> 
                                                        />

                                                            <?php
                                                                if( $split_meta ) {
                                                                    $previous_amount = $item->get_total();
                                                                    include(plugin_dir_path(__FILE__).'modal-edit-item-split-payment.php'); 
                                                                }  
                                                            ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2">
                                                <div  class="actions hidden">
                                                    <button type="button" class="button button-danger" onclick="desactive_edit_price_item();"><?= __('Cancelar','edusystem'); ?></button>
                                                    <button type="submit" id="recalculate_button" class="button button-primary" ><?= __('Recalculate','edusystem'); ?></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            
                            </form>

                            <br/>
                            
                        <?php endif; ?>

                        <?php
                            // hook para mostrar información adicional después de la lista de ítems
                            do_action('after_items_list_payments_edusystem', $order->get_id() );
                        ?>

                        <?php if($order->get_meta('split_payment') && $order->get_meta('split_payment') == 1) { ?>
                            
                            <h2 class="title"><?= __('Split Payment Details','edusystem'); ?></h2>

                            <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
                                <thead>
                                    <tr>
                                        <th scope="col" class="manage-column column-primary column-payment-header"><?= __('Payment','edusystem') ?></th>
                                        <th scope="col" class="manage-column column-payment-method-header"><?= __('Payment Method','edusystem') ?></th>
                                        <th scope="col" class="manage-column column-method-header"><?= __('Method','edusystem') ?></th>
                                        <th scope="col" class="manage-column column-transaction-id-header"><?= __('Transaction ID','edusystem') ?></th>
                                        <th scope="col" class="manage-column column-gross-amount-header"><?= __('Gross amount','edusystem') ?></th>
                                        <th scope="col" class="manage-column column-net-amount-header"><?= __('Net amount','edusystem') ?></th>
                                        <th scope="col" class="manage-column column-fee-payment-method-header"><?= __('Fee payment method','edusystem') ?></th>
                                        <th scope="col" class="manage-column column-status-header"><?= __('Status','edusystem') ?></th>
                                    </tr>
                                </thead>
                                <tbody >

                                    <?php $payments = json_decode($order->get_meta('split_method')); ?>

                                    <?php foreach ($payments as $key => $pay): ?>
                                        <tr>
                                            <td class="column-primary" data-colname="<?= __('Payment','edusystem'); ?>:">
                                                <?= $key + 1 ?>
                                                <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                                            </td>

                                            <td data-colname="<?= __('Payment Method','edusystem'); ?>:">
                                                <?= $pay->method; ?>
                                            </td>

                                            <td data-colname="<?= __('Method','edusystem'); ?>:">
                                                <?= $pay->payment_method ?? '----' ?>
                                            </td>

                                            <td data-colname="<?= __('Transaction ID','edusystem'); ?>:">
                                                <?= $pay->transaction_id ?? '----' ?>
                                            </td>

                                            <td data-colname="<?= __('Gross amount','edusystem'); ?>:">
                                                <?= wc_price($pay->gross_total); ?>
                                            </td>

                                            <td data-colname="<?= __('Net amount','edusystem'); ?>:">
                                                <?= wc_price($pay->amount); ?>
                                            </td>

                                            <td data-colname="<?= __('Fee payment method','edusystem'); ?>:">
                                                <?= wc_price($pay->fee); ?>
                                            </td>

                                            <td data-colname="<?= __('Status','edusystem'); ?>:">
                                                <?= $pay->status == 'completed' || $pay->status == 'complete' ? 'Completed' : ($pay->status == 'refunded' ? 'Refunded' : 'On hold'); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table> 
                            
                            <br/>
                        
                        <?php } else { ?>

                            <div class="seccion-card">
                                <p>
                                    <strong><?=__('Payment Method selected','edusystem')?>:</strong>
                                    <?php 
                                        $split_payment_method = $order->get_meta('split_payment_method', true);
                                        if ($split_payment_method) {
                                            echo esc_html(__('Split payment', 'edusystem'));
                                        } else {
                                            echo  $order->get_payment_method_title(); 
                                        }
                                    ?>
                                </p>
                            </div>

                            <?php if($order->get_meta('_stripe_intent_id')){ ?>
                                <?php if(!in_array('institute',$roles) && !in_array('alliance',$roles)): ?>
                                    <div class="seccion-card">
                                        <p>
                                            <strong><?=__('Transaction ID','edusystem')?>:</strong>
                                            <?= $order->get_meta('_stripe_intent_id'); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            <?php } ?>

                            <?php if($order->get_meta('payment_method')){ ?>
                                <?php if(!in_array('institute',$roles) && !in_array('alliance',$roles)): ?>
                                    <div class="seccion-card">
                                        <p>
                                            <strong><?=__('Payment method used','edusystem')?>:</strong>
                                            <?= $order->get_meta('payment_method'); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            <?php } ?>

                            <?php if($order->get_meta('transaction_id')){ ?>
                                <?php if(!in_array('institute',$roles) && !in_array('alliance',$roles)): ?>
                                    <div class="seccion-card">
                                        <p>
                                            <strong><?=__('Transaction ID','edusystem')?>:</strong>
                                            <?= $order->get_meta('transaction_id'); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            <?php } ?>
                                    
                        <?php } ?>

                        <div class="container-right" >

                            <?php if( !in_array('institutes',$roles) && !in_array('alliance',$roles) ): ?>

                                <?php 
                                    $total_fees = 0;
                                    $total_fees_split = 0;
                                    foreach ( $order->get_fees() as $fee_item ) {

                                        if( $fee_item->get_meta( 'split_payment_method' ) ){
                                            $total_fees_split += $fee_item->get_total();
                                        } else {
                                            $total_fees += $fee_item->get_total();
                                        }
                                    } 
                                ?>

                                <div class="seccion-card">
                                    <p> 
                                        <strong><?=__('Items Subtotal','edusystem')?>:</strong>
                                        <span><?= wc_price( $order->get_subtotal() + $total_fees_split ) ?></span>
                                    </p>
                                </div>

                                <?php if( $order->get_discount_total() ): ?>
                                    <div class="seccion-card">
                                        <p> 
                                            <strong><?=__('Discount','edusystem')?>:</strong>
                                            <span><?= wc_price( $order->get_discount_total() * -1 ) ?></span>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <?php if( $total_fees ): ?>
                                    <div class="seccion-card">
                                        <p> 
                                            <strong><?=__('Fees','edusystem')?>:</strong>
                                            <span><?= wc_price( $total_fees ) ?></span>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <div class="seccion-card">
                                    <p> 
                                        <strong><?=__('Order Total','edusystem')?>:</strong>
                                        <span><?= wc_price( $order->get_total() ) ?></span>
                                    </p>
                                </div>
                                
                                <hr style="width: 50%; margin-left: auto; margin-right: 0;">

                            <?php endif; ?> 

                            <?php if($order->get_meta('split_payment') && $order->get_meta('split_payment') == 1): ?>
                            
                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Total paid gross','edusystem')?>:</strong>
                                        <span><?= wc_price($order->get_meta('total_paid_gross')); ?></span>
                                    </p>
                                </div>

                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Pending payment','edusystem')?>:</strong>
                                        <span><?= wc_price($order->get_meta('pending_payment')); ?></span>
                                    </p>
                                </div>

                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Total paid net','edusystem')?>:</strong>
                                        <span><?= wc_price($order->get_meta('total_paid')); ?></span>
                                    </p>
                                </div>
                                
                            <?php endif; ?>
                            
                            <?php $net_total = $order->get_total() ?>
                            <?php if( $order->get_meta('institute_fee') ): ?>

                                <div class="seccion-card">
                                    <p>
                                        <?php
                                            $institute_fee = intval($order->get_meta('institute_fee') ?? 0);
                                            $net_total -= $institute_fee; 
                                        ?>
                                        <strong>
                                            <?php if( in_array('institute',$roles) ) :?>
                                                <?=__('Fee','edusystem')?>:
                                            <?php elseif( !in_array('alliance',$roles) ): ?>
                                                <?=__('Institute Fee','edusystem')?>:
                                            <?php endif ?>
                                        </strong>
                                        <span><?= wc_price( $institute_fee * -1 ); ?></span>
                                    </p>
                                </div>

                            <?php endif; ?>

                            <?php if(in_array('alliance',$roles) && $order->get_meta('institute_fee')): ?>       
                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Status','edusystem')?>:</strong>
                                        <span><?= $order->get_status() ?></span>
                                    </p>
                                </div>
                            <?php elseif(!in_array('institutes',$roles) && $order->get_meta('institute_fee')): ?>
                                <div class="seccion-card">
                                    <?php
                                        $alliance_fee = intval($order->get_meta('alliance_fee') ?? 0);
                                        $net_total -= $alliance_fee; 
                                    ?>
                                    <p>
                                        <strong><?=__('Alliance Fee','edusystem')?>:</strong>
                                        <span><?= wc_price( $alliance_fee * -1 ); ?></span>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if($order->get_meta('fee_order_pay') && $order->get_meta('fee_order_pay') > 0): ?>
                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Fee payment method','edusystem')?>:</strong>
                                        <span><?= wc_price($order->get_meta('fee_order_pay') * -1); ?></span>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if( !in_array('institutes',$roles) && !in_array('alliance',$roles) ): ?>

                                <?php if( $total_fees ): ?>
                                    <div class="seccion-card">
                                        <p> 
                                            <?php $net_total -= $total_fees; ?>
                                            <strong><?=__('Fees','edusystem')?>:</strong>
                                            <span><?= wc_price( $total_fees * -1 ) ?></span>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <div class="seccion-card">
                                    <p> 
                                        <strong><?=__('Net total','edusystem')?>:</strong>
                                        <span><?= wc_price( $net_total ) ?></span>
                                    </p>
                                </div>

                            <?php endif; ?> 

                        </div>

                        <?php if( !array_intersect(['institutes', 'alliance', 'webinar-aliance'], $roles) ){?>
                            
                            <div id="button-acction-payment" style="margin-top:20px;display:flex;flex-direction:row;width:100%;justify-content:end;">
                                    
                                    <?php if($order->get_status() == 'on-hold'){ ?>
                                        <div style="margin-right: 10px">
                                            <?php if(wp_is_mobile()){ ?>
                                                <button data-message="<?= __('Do you want to decline this payment?','edusystem'); ?>" data-title="<?= __('Decline','edusystem'); ?>" data-id="<?= $order->get_id(); ?>" id="decline_payment" style="width:100%;" class="button button-danger"><?= __('Decline','edusystem'); ?></button>
                                            <?php }else{ ?>
                                                <button data-message="<?= __('Do you want to decline this payment?','edusystem'); ?>" data-title="<?= __('Decline','edusystem'); ?>" data-id="<?= $order->get_id(); ?>" id="decline_payment" class="button button-danger"><?= __('Decline','edusystem'); ?></button>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    
                                    <?php if(($order->get_status() == 'on-hold' || $order->get_status() == 'pending') && ($order->get_meta('split_payment') && $order->get_meta('split_payment') == 1) && ($order->get_meta('pending_payment') && $order->get_meta('pending_payment') > 0)){ ?>
                                        <div style="margin-right: 10px">
                                            <?php if(wp_is_mobile()){ ?>
                                                <button data-total="<?= $order->get_meta('pending_payment') ?>" data-message="<?= __('Do you want to set the date of the next payment?','edusystem'); ?>" data-title="<?= __('Generate next agreed payment','edusystem'); ?>" data-id="<?= $order->get_id(); ?>" id="generate_order_split" style="width:100%;" class="button button-primary"><?= __('Payment agreement','edusystem'); ?></button>
                                            <?php }else{ ?>
                                                <button data-total="<?= $order->get_meta('pending_payment') ?>" data-message="<?= __('Do you want to set the date of the next payment?','edusystem'); ?>" data-title="<?= __('Generate next agreed payment','edusystem'); ?>" data-id="<?= $order->get_id(); ?>" id="generate_order_split" class="button button-primary"><?= __('Payment agreement','edusystem'); ?></button>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>

                                    <?php if($order->get_status() == 'on-hold'){ ?>
                                        <div>
                                            <?php if(wp_is_mobile()){ ?>
                                                <button data-message="<?= __('Do you want to approve this payment?','edusystem'); ?>" data-title="<?= __('Approve','edusystem'); ?>" data-id="<?= $order->get_id(); ?>" id="approved_payment" style="width:100%;" class="button button-success"><?= __('Approve','edusystem'); ?></button>
                                            <?php }else{ ?>
                                                <button data-message="<?= __('Do you want to approve this payment?','edusystem'); ?>" data-title="<?= __('Approve','edusystem'); ?>" data-id="<?= $order->get_id(); ?>" id="approved_payment" class="button button-success"><?= __('Approve','edusystem'); ?></button>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>

                                    <?php if($order->get_status() == 'pending'){ ?>
                                        <div>
                                            <?php if(wp_is_mobile()){ ?>
                                                <button data-message="<?= __('Do you want to approve this payment?','edusystem'); ?>" data-title="<?= __('Approve','edusystem'); ?>" data-id="<?= $order->get_id(); ?>" id="approved_payment" style="width:100%;" class="button button-success"><?= __('Approve','edusystem'); ?></button>
                                            <?php }else{ ?>
                                                <button data-message="<?= __('Do you want to approve this payment?','edusystem'); ?>" data-title="<?= __('Approve','edusystem'); ?>" data-id="<?= $order->get_id(); ?>" id="approved_payment" class="button button-success"><?= __('Approve','edusystem'); ?></button>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                            </div>
                        <?php } ?>

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
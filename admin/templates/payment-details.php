<style>
    .form-table th {
        width: auto !important;
    }
</style>

<div class="wrap edusof-page-admin">
    <h2 style="margin-bottom:15px;"><?= __('Payment details','edusystem'); ?></h2>

    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>
    <div id="notice-payment-completed" style="display:none;" class="notice notice-info"><p><?= __('Payment Completed','edusystem'); ?></p></div>
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">

                <div class="container-card-admin" >
					<div class="card-content" >
						<h2 class="title"><?= __('Payment details','edusystem'); ?></h2>

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
								<?= $order->get_status() ?>
							</p>
						</div>

                        <div class="seccion-card">
							<span class="dashicons dashicons-calendar-alt no-vertical seccion-icon" ></span>
							
                            <p>
								<strong><?=__('Date','edusystem')?>:</strong>
								<?= $order->get_date_created()->date_i18n('Y-m-d H:i:s') ?>
							</p>
						</div>
                        
                        <?php if(!in_array('institutes',$roles) && $student): ?>
                            <div class="seccion-card">
                                <span class="dashicons dashicons-admin-users no-vertical seccion-icon" ></span>
                                
                                <p>
                                    <strong><?=__('Student','edusystem')?>:</strong>
                                    <?= 
                                        $student->last_name . ' ' . 
                                        $student->middle_last_name . ' ' . 
                                        $student->name . ' ' . 
                                        $student->middle_name; 
                                    ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if(!in_array('institutes',$roles)): ?>
                            <div class="seccion-card">
                                <span class="dashicons dashicons-businessperson no-vertical seccion-icon" ></span>
                                
                                <p>
                                    <strong><?=__('Parent','edusystem')?>:</strong>
                                    <?= $order->get_billing_last_name().' '.$order->get_billing_first_name() ?>
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

                <div class="container-card-admin" >
					<div class="card-content" >

                        <div class="card-content-header" >

                            <h2 class="title"><?= __('Payments','edusystem'); ?></h2>

                            <div class="container-button" >
                                <?php
                                    $student = get_student_detail_partner($order->get_customer_id());
                                ?>

                                <a href="<?= admin_url('admin.php?page=add_admin_form_payments_content&section_tab=generate_advance_payment&student_available=1&id_document=') . $student->email ?>"
                                    class="button button-outline-primary">
                                    <?= __('Manage payments', 'edusystem'); ?>
                                </a>
                            </div>
                            
                        </div>

                        <?php if(!in_array('institutes',$roles) && !in_array('alliance',$roles)): ?>

                            <table id="table-products" class="wp-list-table widefat fixed posts striped" style="margin-top:20px;">
                                <thead>
                                    <tr>
                                        <th scope="col" class="manage-column column-primary column-title"><?= __('Program','edusystem') ?></th>
                                        <th scope="col" class="manage-column column-price"><?= __('Total','edusystem') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="table-documents">
                                    <?php foreach($order->get_items() as $item){ ?>
                                        <tr>
                                            <td class="column-primary">
                                                <?= $item->get_name(); ?>
                                            </td>
                                            <td data-colname="<?= __('Total','edusystem'); ?>">
                                                <?= wc_price($item->get_total()); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <br/>
                            
                        <?php endif; ?>

                        <div class="container-right" >

                            <?php if($order->get_meta('fee_order_pay') && $order->get_meta('fee_order_pay') > 0): ?>
                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Fee payment method','edusystem')?>:</strong>
                                        <?= wc_price($order->get_meta('fee_order_pay')); ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if( !in_array('institutes',$roles) && !in_array('alliance',$roles) ): ?>
                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Payment Total','edusystem')?>:</strong>
                                        <?= wc_price($order->get_total()) ?>
                                    </p>
                                </div>
                            <?php endif; ?> 

                            <?php if( in_array('institute',$roles) && $order->get_meta('institute_fee') ): ?>

                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Fee','edusystem')?>:</strong>
                                        <?= wc_price($order->get_meta('institute_fee')); ?>
                                    </p>
                                </div>
                                
                            <?php elseif( !in_array('alliance',$roles) && $order->get_meta('institute_fee') ): ?>
                                    
                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Institute Fee','edusystem')?>:</strong>
                                        <?=  wc_price($order->get_meta('institute_fee')); ?>
                                    </p>
                                </div>

                            <?php endif; ?>

                            <?php if(in_array('alliance',$roles) && $order->get_meta('institute_fee')): ?>       
                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Status','edusystem')?>:</strong>
                                        <?= $order->get_status() ?>
                                    </p>
                                </div>
                            <?php elseif(!in_array('institutes',$roles) && $order->get_meta('institute_fee')): ?>
                                <div class="seccion-card">
                                    <p>
                                        <strong><?=__('Alliance Fee','edusystem')?>:</strong>
                                        <?= wc_price($order->get_meta('alliance_fee')); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            
                        </div>

                        <?php if( !array_intersect(['institutes', 'alliance', 'webinar-aliance'], $roles) ){?>
                            <div style="margin-top:10px;display:flex;flex-direction:row;width:100%;justify-content:end;">
                                    
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

                <div class="container-card-admin" >
					<div class="card-content" >
						<h2 class="title"><?= __('Product','edusystem'); ?></h2>

                        <?php if($order->get_meta('split_payment') && $order->get_meta('split_payment') == 1) { ?>
                            
                            <h2 class="title"><?= __('Split Payment Details','edusystem'); ?></h2>

                            <div class="vertical-section" >
                                <div class="seccion-card">
                                    <span class="dashicons dashicons-marker no-vertical seccion-icon" ></span>
                                    
                                    <p>
                                        <strong><?=__('Total paid net','edusystem')?>:</strong>
                                        <?= wc_price($order->get_meta('total_paid')); ?>
                                    </p>
                                </div>

                                <div class="seccion-card">
                                    <span class="dashicons dashicons-marker no-vertical seccion-icon" ></span>
                                    
                                    <p>
                                        <strong><?=__('Total paid gross','edusystem')?>:</strong>
                                        <?= wc_price($order->get_meta('total_paid_gross')); ?>
                                    </p>
                                </div>

                                <div class="seccion-card">
                                    <span class="dashicons dashicons-marker no-vertical seccion-icon" ></span>
                                    
                                    <p>
                                        <strong><?=__('Pending payment','edusystem')?>:</strong>
                                        <?= wc_price($order->get_meta('pending_payment')); ?>
                                    </p>
                                </div>
                            </div>

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

                                    <?foreach ($payments as $key => $pay): ?>
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
                        
                        <?php } else { ?>

                            <div class="seccion-card">
                                <span class="dashicons dashicons-marker no-vertical seccion-icon" ></span>
                                
                                <p>
                                    <strong><?=__('Payment Method selected','edusystem')?>:</strong>
                                    <?= $order->get_payment_method_title(); ?>
                                </p>
                            </div>

                            <?php if($order->get_meta('_stripe_intent_id')){ ?>
                                <?php if(!in_array('institute',$roles) && !in_array('alliance',$roles)): ?>
                                    <div class="seccion-card">
                                        <span class="dashicons dashicons-marker no-vertical seccion-icon" ></span>
                                        
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
                                        <span class="dashicons dashicons-marker no-vertical seccion-icon" ></span>
                                        
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
                                        <span class="dashicons dashicons-marker no-vertical seccion-icon" ></span>
                                        
                                        <p>
                                            <strong><?=__('Transaction ID','edusystem')?>:</strong>
                                            <?= $order->get_meta('transaction_id'); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            <?php } ?>
                                    
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
<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Report sales','aes'); ?></h2>
    <div style="width:100%;text-align:end;padding-top:10px;">
    
        <?php if(wp_is_mobile()){ ?>
            <select id="typeFilter" name="typeFilter" autocomplete="off" style="min-width:100%;margin-bottom:5px;">
        <?php }else{ ?>
            <select id="typeFilter" name="typeFilter" autocomplete="off">
        <?php } ?>
            <option value="today"><?= __('Today','restaurant-system-app'); ?></option>
            <option value="yesterday"><?= __('yesterday','restaurant-system-app'); ?></option>
            <option value="this-week"><?= __('This week','restaurant-system-app'); ?></option>
            <option value="last-week"><?= __('Last week','restaurant-system-app'); ?></option>
            <option value="this-month" selected><?= __('This month','restaurant-system-app'); ?></option>
            <option value="last-month"><?= __('Last month','restaurant-system-app'); ?></option>
            <option value="custom"><?= __('Custom','restaurant-system-app'); ?></option>
        </select>
        <?php if(wp_is_mobile()){ ?>
            <input type="text" value="<?= $start_date; ?>" id="inputStartDate" style="display:none;width:100%;margin-bottom:5px;">
        <?php }else{ ?>
            <input type="text" value="<?= $start_date; ?>" id="inputStartDate" style="display:none;width:200px;">
        <?php } ?>
        <?php if(wp_is_mobile()): ?>
            <button type="button" id="update_data" class="button button-primary" style="width:100%;"></span><?= __('Update data','restaurant-system-app'); ?></button>
        <?php else: ?>
            <button type="button" id="update_data" class="button button-primary"></span><?= __('Update data','restaurant-system-app'); ?></button>
        <?php endif; ?>
    </div>
	<div style="display: flex; justify-content: center;" id="card-totals-sales">
	<div class="card-report-sales">
			<div>Net total</div><div style="margin-top: 10px"><strong id="total"><?php echo $orders['net_total'] ?></strong></div>
		</div>
		<div class="card-report-sales">
			<div>Fee alliance</div><div style="margin-top: 10px"><strong id="fee-alliance"><?php echo $orders['alliance_fee'] ?></strong></div>
		</div>
		<div class="card-report-sales">
			<div>Fee institute</div><div style="margin-top: 10px"><strong id="fee-institution"><?php echo $orders['institute_fee'] ?></strong></div>
		</div>
		<?php foreach ($orders['payment_methods'] as $key => $paymen_method) { ?>
			<div class="card-report-sales" id="payment-options">
				<div><?php echo $key ?></div><div style="margin-top: 10px"><strong id="<?php echo $key ?>"><?php echo $paymen_method ?></strong></div>
			</div>
		<?php } ?>
	</div>
    <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class=" manage-column column-primary"><?= __('Payment ID','restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Parent','restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Student','restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Total','restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Created','restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Actions','restaurant-system-app'); ?></th>
            </tr>
        </thead>
        <tbody id="table-institutes-payment">
            <?php if(!empty($orders['orders'])){ ?>
                <?php foreach($orders['orders'] as $order){ ?>
                    <tr>
                        <td class="column column-primary" data-colname="<?= __('Payment ID','restaurant-system-app'); ?>">
                            <?= '#'.$order['order_id']; ?>
                            <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                        </td>
                        <td class="column" data-colname="<?= __('Parent','restaurant-system-app'); ?>"><?= $order['customer']; ?></td>
                        <td class="column" data-colname="<?= __('Student','restaurant-system-app'); ?>"><?= $order['student']; ?></td>
                        <td class="column" data-colname="<?= __('Total','restaurant-system-app'); ?>"><?= get_woocommerce_currency_symbol().$order['total']; ?></td>
                        <td class="column" data-colname="<?= __('Created','restaurant-system-app'); ?>"><b><?= $order['created_at']; ?></b></td>
                        <td class="column" data-colname="<?= __('Action','restaurant-system-app'); ?>">
							<a class='button button-primary' href="<?= admin_url('admin.php?page=report-sales&section_tab=payment-detail&payment_id='.$order['order_id']) ?>"><?= __('View details','aes'); ?></a>
                        </td>
                    </tr>
                <?php } ?>
            <?php }else{ ?>
                <tr>
                    <td colspan='5' style='text-align:center;'><?= __('There are not records','aes') ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script>
    document.addEventListener('DOMContentLoaded',function(){
        
        flatpickr(document.getElementById('inputStartDate'), {
            mode: "range",
            dateFormat: "m/d/Y",
            defaultDate: ['<?=  $start_date ?>','<?= $start_date ?>'],
        });
    
    });
</script>
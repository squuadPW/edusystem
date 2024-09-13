<div class="wrap">
    <div style="text-align:start;">
        <h1 class="wp-heading-line"><?= __('Fees','aes'); ?></h1>
    </div> 
    <?php if(!in_array('institutes',$roles) && !in_array('alliance',$roles)){ ?>
        <div style="diplay:flex;width:100%;">
            <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=add_admin_partners_content&section_tab=all_alliances'); ?>"><?= __('Back') ?></a>
        </div>
        <div style="diplay:flex;width:100%;">
            <h3><?= __('Alliance:','aes').' '.$alliance->name.' '.$alliance->last_name; ?></h3>
        </div>
    <?php } ?>
    <div>
        <h3><?= __('Fee Total','aes').': '; ?><span id="fee-total-alliance"><?= get_woocommerce_currency_symbol().$orders['total']; ?></span></h3>
    <div>  
    <div style="width:100%;text-align:end;padding-top:10px;">
    
        <?php if(wp_is_mobile()){ ?>
            <select id="typeFilter" name="typeFilter" autocomplete="off" style="min-width:100%;margin-bottom:5px;">
        <?php }else{ ?>
            <select id="typeFilter" name="typeFilter" autocomplete="off">
        <?php } ?>
            <option value="today" selected><?= __('Today','restaurant-system-app'); ?></option>
            <option value="yesterday"><?= __('yesterday','restaurant-system-app'); ?></option>
            <option value="this-week"><?= __('This week','restaurant-system-app'); ?></option>
            <option value="last-week"><?= __('Last week','restaurant-system-app'); ?></option>
            <option value="this-month"><?= __('This month','restaurant-system-app'); ?></option>
            <option value="last-month"><?= __('Last month','restaurant-system-app'); ?></option>
            <option value="custom"><?= __('Custom','restaurant-system-app'); ?></option>
        </select>
        <?php if(wp_is_mobile()){ ?>
            <input type="text" value="<?= $start_date; ?>" id="inputStartDate" style="display:none;width:100%;margin-bottom:5px;">
        <?php }else{ ?>
            <input type="text" value="<?= $start_date; ?>" id="inputStartDate" style="display:none;width:200px;">
        <?php } ?>
        <?php if(wp_is_mobile()): ?>
            <button type="button" id="update_data" class="button button-primary" style="width:100%"></span><?= __('Update data','restaurant-system-app'); ?></button>
        <?php else: ?>
            <button type="button" id="update_data" class="button button-primary"></span><?= __('Update data','restaurant-system-app'); ?></button>
        <?php endif; ?>
    </div>
    <input type="hidden" id="alliance_id" value="<?= (isset($_GET['alliance_id']) && !empty($_GET['alliance_id'])) ? $_GET['alliance_id'] : ''; ?>">
    <table class="wp-list-table widefat fixed striped posts" style="margin-top:20px;">
        <thead>
            <tr>
                <th scope="col" class=" manage-column column-primary"><?= __('Payment ID','restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-email"><?= __('Customer','restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column-phone"><?= __('Fee','restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Created','restaurant-system-app'); ?></th>
                <th scope="col" class=" manage-column column"><?= __('Actions','restaurant-system-app'); ?></th>
            </tr>
        </thead>
        <tbody id="table-payment-alliance">
            <?php if(!empty($orders['orders'])){ ?>
                <?php foreach($orders['orders'] as $order){ ?>
                    <tr>
                        <td class="column column-primary" data-colname="<?= __('Payment ID','restaurant-system-app'); ?>">
                            <?= '#'.$order['order_id']; ?>
                            <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                        </td>
                        <td class="column" style="align-content:center;" data-colname="<?= __('Customer','restaurant-system-app'); ?>"><?= $order['customer']; ?></td>
                        <td class="column" style="align-content:center;" data-colname="<?= __('Fee','restaurant-system-app'); ?>"><?= get_woocommerce_currency_symbol().number_format($order['fee'],2,'.',','); ?></td>
                        <td class="column" style="align-content:center;" data-colname="<?= __('Created','restaurant-system-app'); ?>"><b><?= $order['created_at']; ?></b></td>
                        <td class="column" style="align-content:center;" data-colname="<?= __('Action','restaurant-system-app'); ?>">
                            <?php if(isset($_GET['alliance_id'])): ?>
                                <a class='button button-primary' href="<?= admin_url('admin.php?page=add_admin_partners_content&section_tab=payment-detail&payment_id='.$order['order_id']) ?>"><?= __('View details','aes'); ?></a>
                            <?php else: ?>
                                <a class='button button-primary' href="<?= admin_url('admin.php?page=list_admin_partner_payments_content&action=payment-detail&payment_id='.$order['order_id']) ?>"><?= __('View details','aes'); ?></a>
                            <?php endif; ?>
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
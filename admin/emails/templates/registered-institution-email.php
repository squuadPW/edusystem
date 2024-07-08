<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    <p><?php printf( esc_html__( 'A new institution has registered ', 'aes' )); ?></p>

    <div style="margin-bottom: 40px;">
        <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <tfoot>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Name','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->name; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Phone','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->phone; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Email','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->email; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Country','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= get_name_country($institution->country); ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('State','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->state ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('City','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->city; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Address','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->address; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Level','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= get_name_level($institution->level_id) ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="3" style="text-align:center;" ><?= __('Contact','aes'); ?></th>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Rector\'s name','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->name_rector; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Rector\'s last name','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->lastname_rector; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Rector Phone','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->phone_rector; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="3" style="text-align:center;" ><?= __('Reference','aes'); ?></th>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Reference Type','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= get_name_reference($institution->reference); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
<?php 
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
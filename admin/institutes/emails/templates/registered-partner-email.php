<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    <p><?php printf( esc_html__( 'A new alliance has registered.', 'edusystem' )); ?></p>

    <div style="margin-bottom: 40px;">
        <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <tfoot>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Name','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $alliance->name; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Last Name','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $alliance->last_name; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Name of legal representative','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $alliance->name_legal; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Contact Number','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $alliance->phone ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Email','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $alliance->email; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Country','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= get_name_country($alliance->country); ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('State','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $alliance->state ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('city','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $alliance->city; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Address','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $alliance->address; ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
<?php 
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    <p><?php printf( esc_html__( 'A new institution has registered ', 'edusystem' )); ?></p>

    <div style="margin-bottom: 40px;">
        <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <tfoot>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Name','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->name; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Phone','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->phone; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Email','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->email; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Country','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= get_name_country($institution->country); ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('State','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->state ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('City','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->city; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Address','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->address; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Level','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= get_name_level($institution->level_id) ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="3" style="text-align:center;" ><?= __('Contact','edusystem'); ?></th>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Rector\'s name','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->name_rector; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Rector\'s last name','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->lastname_rector; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Rector Phone','edusystem'); ?></th>
                    <td class="td" style="text-align:right;"><?= $institution->phone_rector; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="3" style="text-align:center;" ><?= __('Reference','edusystem'); ?></th>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Reference Type','edusystem'); ?></th>
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
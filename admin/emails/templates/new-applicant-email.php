<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    <p><?php printf( esc_html__( 'A new student has registered.', 'aes' )); ?></p>

    <div style="margin-bottom: 40px;">
        <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <tfoot>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Name','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $student->name; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Last Name','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $student->last_name; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Email','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $student->email; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Phone','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $student->phone; ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Program','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= get_name_program($student->program_id); ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Grade','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= get_name_grade($student->grade_id); ?></td>
                </tr>
                <tr>
                    <th class="td" scope="row" colspan="2" style="text-align:left;" ><?= __('Institution Name','aes'); ?></th>
                    <td class="td" style="text-align:right;"><?= $student->name_institute; ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
<?php 
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );


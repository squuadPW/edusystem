<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    <p><?php printf( esc_html__( 'documents have been received from the student %s.', 'aes' ),$student->name.' '.$student->last_name); ?></p>

    <p><?= __('The following documents are required to be reviewed','aes').':'; ?></p>

    <div style="margin-bottom: 40px;">
        <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
            <thead>
                <tr>
                    <th class="td" scope="col" style="text-decoration: none;font-size:13px;text-align:left;"><?= __( 'Document', 'aes' ); ?></th>
                    <th class="td" scope="col" style="text-decoration: none;font-size:13px;text-align:right;"><?= __( 'Status', 'aes' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($documents as $document){ ?>
                    <tr>
                        <td class="td" scope="row" style="font-size:13px;text-decoration: none;text-align:left;"><?= get_name_document($document->document_id); ?></td>
                        <td class="td" scope="row" style="font-size:13px;text-decoration: none;text-align:right;"><?=  get_status_document($document->status); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php 
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );

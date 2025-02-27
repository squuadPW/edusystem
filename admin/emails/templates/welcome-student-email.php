<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    
<?php
// Email template
$email_template = '
<html>
  <body>
    <p>Dear ' . $student->name . ' ' . $student->last_name . ',</p>

    <p>We are excited to have you join us. To get started, please access the classroom using the link provided and create your password. This will allow you to explore all the resources and activities we have prepared for you.</p>

    <p>We put at your disposal links and contacts of interest:</p>
    
    <p>Links:</p>
    <ul>
      <li>Website: <a href="https://americanelite.school/">https://americanelite.school/</a></li>
      <li>Virtual classroom: <a href="https://portal.americanelite.school/">https://portal.americanelite.school/</a></li>
    </ul>

    <p>Enter here to create your password:</p>
    <ul>
      <li>User: '. $student->email . '</li>
      <li>Set password: <a href="'. $reset_url .'">'. $reset_url .'</a></li>
    </ul>
    
    <p>Contact:</p>
    <ul>
      <li>Support: <a href="https://support.americanelite.school/">https://support.americanelite.school</a></li>
    </ul>
    
    <p>On behalf of the entire American Elite School community, we thank you for your trust and commitment, certain of our contribution by providing you with exceptional educational opportunities and contributing to your training, so that you achieve success in your future careers and in your professional and personal life.</p>
    
    <p style="border-bottom: 1px solid gray;">Kind regards.</p>
  </body>
</html>
';

// Spanish version
$email_template_es = '
<html>
  <body>
    <p>Estimado(a) ' . $student->name . ' ' . $student->last_name . ',</p>

    <p>Estamos emocionados de tenerte con nosotros. Para comenzar, por favor accede al aula utilizando el enlace proporcionado y crea tu contraseña. Esto te permitirá explorar todos los recursos y actividades que hemos preparado para ti.</p>

    <p>Ponemos a su disposición enlaces y contactos de interés:</p>
    
    <p>Enlaces:</p>
    <ul>
      <li>Sitio web: <a href="https://americanelite.school/">https://americanelite.school/</a></li>
      <li>Aula virtual: <a href="https://portal.americanelite.school/">https://portal.americanelite.school/</a></li>
    </ul>

    <p>Ingresa aqui para crear tu contraseña:</p>
    <ul>
      <li>Usuario: <strong>'. $student->email . '</strong></li>
      <li>Crear contraseña: <a href="'. $reset_url .'">'. $reset_url .'</a></li>
    </ul>
    
    <p>Contacto:</p>
    <ul>
      <li>Soporte: <a href="https://support.americanelite.school/">https://support.americanelite.school</a></li>
    </ul>
    
    <p>En nombre de toda la comunidad de American Elite School, le agradecemos por su confianza y compromiso, seguros de nuestra contribución al brindarles oportunidades educativas excepcionales y aportando en su formación, para que alcancen el éxito en sus futuras carreras y en la vida profesional y personal.</p>
    
    <p>Saludos cordiales.</p>
  </body>
</html>
';

// Output the email template
echo $email_template;
echo $email_template_es;

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );


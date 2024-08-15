<?php
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

?>
    

    <!-- <p>On behalf of American Elite School, based in the city of Doral, Florida, USA, we wish to inform you of the start of the next academic period 2024-2025.</p>
    
    <p>We are pleased to inform you that Academic Activities begin on Monday, August 12, 2024.</p>
    
    <p>We continue to innovate and from this period onwards, we will offer new features and innovations that will facilitate your experience with our Virtual Learning Environment.</p>
    
    <p>On Monday, August 12, 2024, at 3:30 p.m. (Miami Time) a Virtual Welcome Meeting is scheduled to inform you of the aspects inherent to the use of your Virtual Classroom, Navigation, Resources, Interaction Dynamics and important considerations about our Educational Model adapted to our modality. Virtual study.</p> -->


    <!-- <p>En nombre de American Elite School, con sede en la ciudad de Doral, Florida, EE.UU., deseamos informar el inicio del próximo periodo académico 2024-2025.</p>
    
    <p>Nos complace informarles que las Actividades Académicas inician el día lunes 12 de agosto de 2024.</p>
    
    <p>Seguimos innovando y a partir de este período, ofreceremos nuevas prestaciones y novedades que facilitarán su experiencia con nuestro Entorno Virtual de Aprendizaje.</p>
    
    <p>El día lunes 12 de agosto de 2024, a las 15:30 (Hora de Miami), se encuentra programada una Reunión Virtual de Bienvenida, para darles a conocer los aspectos inherentes al uso de su Aula Virtual, Navegación, Recursos, Dinámicas de Interacción y consideraciones importantes sobre nuestro Modelo Educativo adaptado a nuestra modalidad de estudio Virtual.</p> -->

<?php
// Email template
$email_template = '
<html>
  <body>
    <p>Dear ' . $student->name . ' ' . $student->last_name . ',</p>
    
    <p>We are pleased to inform you that we have successfully transitioned to the new American Elite platform. As part of this process, all your data has been migrated to the new platform.<p>

    <p>We put at your disposal links and contacts of interest:</p>
    
    <p>Links:</p>
    <ul>
      <li>Website: <a href="https://american-elite.us/">https://american-elite.us/</a></li>
      <li>Virtual classroom: <a href="https://online.american-elite.us/">https://online.american-elite.us/</a></li>
    </ul>

    <p>Enter here to create your password:</p>
    <ul>
      <li>User: '. $student->email . '</li>
      <li>Set password: <a href="'. $reset_url .'">'. $reset_url .'</a></li>
    </ul>
    
    <p>Contacts:</p>
    <ul>
      <li>Academic Support: <a href="mailto:academic.support@american-elite.us">academic.support@american-elite.us</a></li>
      <li>Student Services: <a href="mailto:student.services@american-elite.us">student.services@american-elite.us</a></li>
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
    
    <p>Nos complace informarles que hemos completado con éxito nuestra transición a la nueva plataforma American Elite. Como parte de este proceso, todos sus datos han sido migrados con éxito a la nueva plataforma.<p>

    <p>Ponemos a su disposición enlaces y contactos de interés:</p>
    
    <p>Enlaces:</p>
    <ul>
      <li>Sitio web: <a href="https://american-elite.us/">https://american-elite.us/</a></li>
      <li>Aula virtual: <a href="https://online.american-elite.us/">https://online.american-elite.us/</a></li>
    </ul>

    <p>Ingresa aqui para crear tu contraseña:</p>
    <ul>
      <li>Usuario: <strong>'. $student->email . '</strong></li>
      <li>Crear contraseña: <a href="'. $reset_url .'">'. $reset_url .'</a></li>
    </ul>
    
    <p>Contactos:</p>
    <ul>
      <li>Soporte Académico: <a href="mailto:academic.support@american-elite.us">academic.support@american-elite.us</a></li>
      <li>Servicios Estudiantiles: <a href="mailto:student.services@american-elite.us">student.services@american-elite.us</a></li>
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


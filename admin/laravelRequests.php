<?php

function create_user_laravel($data)
{
  $url = URL_LARAVEL_PPADMIN;
  $ch = curl_init($url . 'api/public/create-student');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  
  // Preparar los archivos para enviar
  $files_to_send = $data['files'];
  $fields_to_send = array_diff_key($data, array('files' => ''));

  $data = array_merge($fields_to_send, []);

  foreach ($files_to_send as $file) {
    $data[$file['id_requisito']] = $file['file'];
  }

  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  $response = curl_exec($ch);
  
  // // Obtener información sobre la respuesta
  // $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  // $error_number = curl_errno($ch);
  // $error_message = curl_error($ch);
  
  // // Imprimir la información
  // echo "Código HTTP: $http_code\n";
  // echo "Número de error: $error_number\n";
  // echo "Mensaje de error: $error_message\n";
  // echo "Respuesta: $response\n";
  
  curl_close($ch);
}

function update_user_laravel($data)
{
  $url = URL_LARAVEL_PPADMIN;
  $ch = curl_init($url . 'api/public/update-student');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  $response = curl_exec($ch);

  curl_close($ch);
}

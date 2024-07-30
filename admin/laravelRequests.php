<?php

function create_user_laravel($fields, $files) {
  // $url = 'https://ppadmin.american-elite.us/';
  $url = 'https://5128-190-142-88-208.ngrok-free.app/';

  $ch = curl_init($url.'api/public/create-student');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);

  $post_data = array();
  foreach ($fields as $key => $value) {
      $post_data[$key] = $value;
  }
  foreach ($files as $key => $file) {
    $post_data[$key] = $file;
  }
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

  $response = curl_exec($ch);
  $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  if (curl_errno($ch)) {
    //   echo 'Error: ' . curl_error($ch);
  } else {
    //   echo "Response code: $response_code";
    //   echo "Response body: $response";
  }

  curl_close($ch);
}
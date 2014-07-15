<?php
$email = $_POST ['email'];
$usuario = $_POST ['usuario'];
$institucion = $_POST ['institucion'];

$to = 'matalia85@gmail.com';

// subject
$subject = 'Solicitud de contraseña: ' . $usuario;

// message
$message = '
<html>
<head>
  <title>Recuperar de contraseña</title>
</head>
<body>
  <p>Favor contactar al usuario: <em>' . $usuario . '</em>, en la institucion: <em>' . $institucion . '</em>, e-mail registrado: <em>' . $email . '</em> , verificar información y proporcionar nueva contraseña para el sistema Biosoft Unidad Transfusional.</p>
  
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: Biosoft <admin@biosoft.com>' . "\r\n";

// Mail it
mail ( $to, $subject, $message, $headers );
?>
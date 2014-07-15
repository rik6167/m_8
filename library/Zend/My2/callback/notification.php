<?php
/**
 * funcion encargada de enviar un correo de notificacion cuando se ingresa o modifica un usuario
 */
function notification()
{

    $correo  = isset($_POST['correo'])?$_POST['correo']:'';
    $name    = isset($_POST['fullname'])?$_POST['fullname']:'';
    $usuario = isset($_POST['usuario'])?$_POST['usuario']:'';
    $clave   = isset($_POST['clave'])?$_POST['clave']:'';
    $sendmail= isset($_POST['sendmail'])?$_POST['sendmail']:'';
    if(false == empty($clave) && ($sendmail == 'Y')){
        
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText("Sr(a) {$name}, esta es su información de ingreso para el sistema Enlazamundos\r\n\r\nUsuario:{$usuario}\r\nClave:{$clave}");
        $mail->setFrom('programaenlazamundos@cta.org.co  ', 'Programa Enlazamundos');
        $mail->addTo($correo);
        $mail->setSubject('Confirmación de registro Enlazamundos');
        $mail->send();
        return;
    }
}
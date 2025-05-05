<?php

require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ob_start();
include_once('../../backend/emailtemplate.php');
$corpo_email = ob_get_clean();

$mail = new PHPMailer(true);

try{
    //Configurações do servidor SMTP
    $mail->isSMTP();
    $mail->Host     = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Username = '4a816f1cc0a3a6';
    $mail->Password = 'd6bb4721ea59ff';
    $mail->Port     = 587;

    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->setFrom('afmferreira2007@gmail.com', 'qrschools');
    $mail->addAddress('josegoncalves@esjaloures.org', 'Jarg');

    $mail->isHTML(true);
    $mail->Subject = 'Assunto do email html';
    $mail->Body = $corpo_email;
    $mail->AltBody = 'Esté é o corpo do e-mail em texto simples (para clientes que nao suportam HTML).';

    $mail->send();
    echo('Email enviado com sucesso');
}catch (Exception $e){
    echo('Erro ao enviar {$email->ErrorInfo}');
}

?>

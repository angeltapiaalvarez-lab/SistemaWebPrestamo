<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail(string $to, string $toName, string $subject, string $body, ?string $fromEmail = null, ?string $fromName = null): bool
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER');
        $mail->Password   = getenv('SMTP_PASS');
        $mail->Port       = getenv('SMTP_PORT') ?: 465;

                // Leer el tipo de encriptación desde la variable de entorno
        $smtpSecure = strtolower(getenv('SMTP_SECURE') ?: 'ssl');
        if ($smtpSecure === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($smtpSecure === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // por defecto
        }


        $mail->setFrom($fromEmail ?? getenv('SMTP_FROM'), $fromName ?? getenv('SMTP_FROM_NAME') ?: 'Sistema');
        $mail->addAddress($to, $toName);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        log_message('error', 'Error enviando correo: ' . $mail->ErrorInfo);
        return false;
    }
}

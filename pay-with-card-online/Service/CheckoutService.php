<?php

namespace JuraganTulangRangu;

use JuraganTulangRangu\Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CheckoutService
{

    function sendOrderEmail($subject, $cartItemsArray, $shippingAmount, $customerDetailsArray, $recipientArr, $recipientCCArr, $recipientBCCArr = Config::BCC_EMAIL)
    {
        require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
        require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';

        $mail = new PHPMailer(true); // ✅ Perubahan: gunakan true agar support try-catch

        if (!is_array($recipientBCCArr)) {
            $recipientBCCArr = array_map('trim', explode(',', $recipientBCCArr));
        }
        
        try {
            // ✅ Konfigurasi SMTP ditambahkan
            $mail->isSMTP();
            $mail->Host = Config::SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = Config::SMTP_USERNAME;
            $mail->Password = Config::SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // bisa juga 'ssl'
            $mail->Port = Config::SMTP_PORT;

            // ✅ Tambahan: timeout dan debug
            $mail->Timeout = 10;
            $mail->SMTPDebug = 0; // Ubah jadi 2 jika ingin debugging saat dev
            $mail->Debugoutput = 'error_log'; // simpan output debug ke log server

            // ✅ Penerima
            foreach ($recipientArr as $recipient) {
                if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress($recipient);
                }
            }

            foreach ($recipientCCArr as $cc) {
                if (filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                    $mail->addCC($cc);
                }
            }

            foreach ($recipientBCCArr as $bcc) {
                if (filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
                    $mail->addBCC($bcc);
                }
            }

            // ✅ Set email
            $mail->isHTML(true);
            $mail->Subject = $subject;

            require_once __DIR__ . '/../resource/mail/order-confirmation.php';
            $emailBodyHtml = getOrderBody($cartItemsArray, $customerDetailsArray, $shippingAmount);
            $mail->Body = $emailBodyHtml;

            $replyToEmail = Config::SENDER_EMAIL;
            $replyToName = Config::SENDER_NAME;
            $mail->setFrom($replyToEmail, $replyToName);
            $mail->addReplyTo($replyToEmail, $replyToName);

            // ✅ Kirim email
            $mailResult = $mail->send();
            return $mailResult;

        } catch (Exception $e) {
            error_log("Email gagal dikirim: " . $mail->ErrorInfo);
            return false;
        }
    }
    function sanitizeEmails($emailArray)
    {
        $cleanEmailArray = array();
        foreach ($emailArray as $email) {
            $cleanEmail = trim($email);
            if (! empty($cleanEmail)) {
                filter_var($cleanEmail, FILTER_SANITIZE_EMAIL);
                $cleanEmailArray[] = $cleanEmail;
            }
        }
        return $cleanEmailArray;
    }

    function getToken()
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < 17; $i ++) {
            $token .= $codeAlphabet[mt_rand(0, $max)];
        }
        return $token;
    }
}
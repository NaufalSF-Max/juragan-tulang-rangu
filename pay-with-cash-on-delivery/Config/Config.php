<?php

namespace Foodboard;

class Config
{

    const APP_ROOT = 'http://localhost';

    const WORK_ROOT = '/projek-umkm/';

    const THANKYOU_URL = Config::WORK_ROOT . 'pay-with-cash-on-delivery/thank-you.php';

    const CANCEL_URL = Config::WORK_ROOT . 'pay-with-cash-on-delivery/index.php';

    const ORDER_EMAIL_SUBJECT = 'Order Confirmation';

    const CURRENCY = 'IDR';

    const CURRENCY_SYMBOL = 'Rp';

    /* Sender and Recipient
    ==================================== */

    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587;
    const SMTP_USERNAME = 'naufalsafiqq@gmail.com'; // akun Gmail
    const SMTP_PASSWORD = 'muzo ffhd trms uvte'; // dari langkah 2

    const SENDER_NAME = 'Juragan Tulang Rangu Karawang';

    const SENDER_EMAIL = 'noreply@juragantulangrangu.com';

    // You can add one or more emails separated by a comma (,)
    // To whom the contact form should send the email, generally the Admin of the site
    const RECIPIENT_EMAIL = 'juragantulangrangu@gmail.com';

    // If you want to send the same email message as a copy (CC), then enter the email(s) in below option
    const CC_EMAIL = '';

    // If you want to send the same email message as a blind copy (BCC), then enter the email(s) in below option
    const BCC_EMAIL = '';

    /* Mechanism to use, to send email  
    ==================================== */
    // Options: smtp, sendmail, phpmail. Default is smtp but FoodBoard implemented to use only the phpmail option
    // phpmail uses your web server's default email server setup to send email
    const MAILER = 'phpmail';
}
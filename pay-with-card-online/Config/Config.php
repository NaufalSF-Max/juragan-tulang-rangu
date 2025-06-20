<?php

namespace JuraganTulangRangu;

class Config
{

    const ORDER_EMAIL_SUBJECT = 'Order Confirmation';

    // const CURRENCY = 'IDR';

    const CURRENCY_SYMBOL = 'Rp';

    /* Sender and Recipient
    ==================================== */

    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587;
    const SMTP_USERNAME = 'naufalsafiqq@gmail.com'; // akun Gmail
    const SMTP_PASSWORD = 'muzo ffhd trms uvte'; // dari langkah 2

    const SENDER_NAME = 'Juragan Tulang Rangu Karawang';

    const SENDER_EMAIL = 'noreply@tulangrangukarawang.com';

    // You can add one or more emails separated by a comma (,)
    // To whom the contact form should send the email, generally the Admin of the site
    const RECIPIENT_EMAIL = 'tulangrangukarawang@gmail.com';

    const CC_EMAIL = '';

    const BCC_EMAIL = 'naufalsafiqq@gmail.com';

    const MAILER = 'phpmail';
}
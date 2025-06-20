<?php

use JuraganTulangRangu\Config;

function getOrderBody($cartItemsArray, $customerDetailsArray, $shippingAmount)
{
    ob_start();
?>

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Order Confirmation</title>
        <style>
            .tbl-cart {
                margin-top: 20px;
                margin-bottom: 20px;
            }
        </style>
    </head>

    <body itemscope itemtype='http://schema.org/EmailMessage' style="margin: 0; font-family: 'HelveticaNeue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 13px; color: #616161; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100%; height: 100%; line-height: 1.6em; background-color: #FFF;">

        <table style='vertical-align: top; width: 100%;'>
            <tr>
                <td style='vertical-align: top;'></td>
                <td width='900px' style='vertical-align: top; padding: 0 !important; width: 100% !important;'>
                    <div style='max-width: 1200px; display: block; padding: 20px;'>
                        <table width='100%' cellpadding='0' cellspacing='0' itemprop='action' itemscope itemtype='http://schema.org/ConfirmAction' style='background-color: #fff; border-radius: 2px;'>
                            <tr>
                                <td style='padding: 20px;'>
                                    <meta itemprop='name' content='Confirm Email' />
                                    <table width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td>
                                                <?php if (!empty($cartItemsArray)) { ?>
                                                    <div style="padding: 10px;"></div>
                                                    <div>
                                                        <h3>Thank you for your order!</h3>
                                                        <div>
                                                            <h5>Your ordered items are:</h5>
                                                        </div>
                                                        <table style="border: 1px solid #E0E0E0; border-radius: 3px;" cellpadding="10" cellspacing="0">
                                                            <tbody style="border: 1px solid #E0E0E0; border-radius: 3px;">
                                                                <tr>
                                                                    <th style="padding: 5px 10px; border-bottom: 1px #E0E0E0 solid; border-right: 1px #E0E0E0 solid; text-align: left" width="45%">Title</th>
                                                                    <th style="padding: 5px 10px; border-bottom: 1px #E0E0E0 solid; border-right: 1px #E0E0E0 solid; text-align: right" width="15%">Unit Price (<?= Config::CURRENCY_SYMBOL ?>)</th>
                                                                    <th style="padding: 5px 10px; border-bottom: 1px #E0E0E0 solid; border-right: 1px #E0E0E0 solid; text-align: right" width="15%">Quantity</th>
                                                                    <th style="padding: 5px 10px; border-bottom: 1px #E0E0E0 solid; text-align: right" width="15%">Total Price (<?= Config::CURRENCY_SYMBOL ?>)</th>
                                                                </tr>
                                                                <?php
                                                                $total_price_array = [];

                                                                foreach ($cartItemsArray as $item) {
                                                                    $title = $item['name'];
                                                                    $variant = $item['variant'];
                                                                    $qty = $item['quantity'];
                                                                    $basePrice = (int)$item['price'];

                                                                    $extraPrice = 0;
                                                                    $extraLabels = [];

                                                                    if (!empty($item['extras'])) {
                                                                        if (!empty($item['extras']) && is_array($item['extras'])) {
                                                                        foreach ($item['extras'] as $extraLabel) {
                                                                            $extraPrice += 0; // Harga sudah include
                                                                            $extraLabels[] = $extraLabel;
                                                                        }
                                                                    }
                                                                    }

                                                                    $unitPrice = $basePrice + $extraPrice;
                                                                    $lineTotal = $unitPrice * $qty;
                                                                    $total_price_array[] = $lineTotal;

                                                                    $titleFull = $title . " - " . $variant;
                                                                    if (!empty($extraLabels)) {
                                                                        $titleFull .= "<br><small>Extras: " . implode(', ', $extraLabels) . "</small>";
                                                                    }
                                                                ?>
                                                                    <tr>
                                                                        <td style="padding: 5px 10px; border-bottom: 1px solid #E0E0E0; border-right: 1px solid #E0E0E0;"><?php echo $titleFull; ?></td>
                                                                        <td style="padding: 5px 10px; border-bottom: 1px solid #E0E0E0; border-right: 1px solid #E0E0E0; text-align: right;"><?php echo number_format($unitPrice, 2); ?></td>
                                                                        <td style="padding: 5px 10px; border-bottom: 1px solid #E0E0E0; border-right: 1px solid #E0E0E0; text-align: right;"><?php echo $qty; ?></td>
                                                                        <td style="padding: 5px 10px; border-bottom: 1px solid #E0E0E0; text-align: right;"><?php echo number_format($lineTotal, 2); ?></td>
                                                                    </tr>
                                                                <?php } ?>

                                                                <?php
                                                                $sub_total_price = array_sum($total_price_array);
                                                                $total_price = $sub_total_price + $shippingAmount;
                                                                ?>

                                                                <tr class="sub_total">
                                                                    <td colspan="2" style="padding: 5px 10px; border-bottom: 1px solid #E0E0E0; text-align: right"><strong>Delivery Fee (<?= Config::CURRENCY_SYMBOL ?>)</strong></td>
                                                                    <td colspan="2" style="padding: 5px 10px; border-bottom: 1px solid #E0E0E0; text-align: right"><strong><?php echo number_format($shippingAmount, 2); ?></strong></td>
                                                                </tr>
                                                                <tr class="sub_total">
                                                                    <td colspan="2" style="padding: 5px 10px; text-align: right"><strong>Grand Total (<?= Config::CURRENCY_SYMBOL ?>)</strong></td>
                                                                    <td colspan="2" style="padding: 5px 10px; text-align: right"><strong><?php echo number_format($total_price, 2); ?></strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <h3>Customer details:</h3>
                                                        <?php
                                                        $email = "";
                                                        foreach ($customerDetailsArray as $k => $v) {
                                                            if ($k == "email") {
                                                                $email = $v;
                                                            }
                                                        ?>
                                                            <div>
                                                                <strong><?php echo ucfirst($k); ?>: </strong><span><?php echo htmlspecialchars($v); ?></span>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <div width='100%' cellpadding='0' cellspacing='0' style="margin-top: 20px; padding: 20px; background-color: #fff;">
                            <p>If you have questions about your order or general feedback, please contact us.</p>
                            <hr />
                            <i>This email was sent to <?= htmlspecialchars($email) ?>.
                                This email is an order confirmation and product delivery email.
                                It is not a marketing or promotional email and does not contain an unsubscribe link.</i>
                        </div>
                    </div>
                </td>
                <td></td>
            </tr>
        </table>
    </body>

    </html>

<?php
    return ob_get_clean();
}
?>
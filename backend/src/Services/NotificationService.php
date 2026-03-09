<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class NotificationService
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password = $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
        $this->mailer->Port = $_ENV['MAIL_PORT'];
        $this->mailer->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    }

    public function sendOrderReceipt($email, $order, $entitlements)
    {
        try {
            $this->mailer->addAddress($email);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Your Order Confirmation - Musician Storefront';
            
            $html = "<h1>Thank you for your purchase!</h1>";
            $html .= "<p>Order ID: #{$order->id}</p>";
            $html .= "<p>Total Amount: {$order->total_amount} {$order->currency_code}</p>";
            $html .= "<h3>Your Downloads:</h3><ul>";
            
            foreach ($entitlements as $entitlement) {
                $song = $entitlement->song;
                $downloadUrl = "https://yourdomain.com/api/buyer/download/{$song->id}"; 
                // For guests, we should ideally use the access_token here
                if ($entitlement->access_token) {
                    $downloadUrl .= "?token=" . $entitlement->access_token;
                }
                $html .= "<li>{$song->title} - <a href='{$downloadUrl}'>Download</a></li>";
            }
            $html .= "</ul>";

            $this->mailer->Body = $html;
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}

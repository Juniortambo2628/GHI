<?php

/**
 * Mail Service using Symfony Mailer
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailService
{
    private static ?Mailer $instance = null;

    /**
     * Get Mailer instance (Singleton)
     */
    public static function getInstance(): Mailer
    {
        if (!self::$instance instanceof \Symfony\Component\Mailer\Mailer) {
            self::$instance = self::createMailer();
        }

        return self::$instance;
    }

    /**
     * Create and configure Mailer
     */
    private static function createMailer(): Mailer
    {
        $dsn = defined('MAILER_DSN') ? MAILER_DSN : 'smtp://localhost:1025';
        $transport = Transport::fromDsn($dsn);

        return new Mailer($transport);
    }

    /**
     * Send email
     */
    public static function send(
        string $to,
        string $subject,
        string $body,
        string $from = null,
        string $fromName = null,
        bool $isHtml = true
    ): bool {
        try {
            $from ??= defined('SITE_EMAIL') ? SITE_EMAIL : 'noreply@example.com';
            $fromName ??= defined('SITE_NAME') ? SITE_NAME : 'Global Harmony Initiative';

            $email = (new Email())
                ->from(new Address($from, $fromName))
                ->to($to)
                ->subject($subject);

            if ($isHtml) {
                $email->html($body);
            } else {
                $email->text($body);
            }

            self::getInstance()->send($email);

            if (function_exists('log_message')) {
                log_message('info', 'Email sent successfully', [
                    'to' => $to,
                    'subject' => $subject,
                ]);
            }

            // Dispatch email sent event
            if (class_exists(\GHI\Events\EmailSentEvent::class)) {
                $event = new \GHI\Events\EmailSentEvent($to, $subject, true);
                if (function_exists('event_dispatch')) {
                    event_dispatch($event, \GHI\Events\EmailSentEvent::NAME);
                }
            }

            return true;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Email send failed', [
                    'to' => $to,
                    'subject' => $subject,
                    'error' => $exception->getMessage(),
                ]);
            }

            // Dispatch email failed event
            if (class_exists(\GHI\Events\EmailSentEvent::class)) {
                $event = new \GHI\Events\EmailSentEvent($to, $subject, false);
                if (function_exists('event_dispatch')) {
                    event_dispatch($event, \GHI\Events\EmailSentEvent::NAME);
                }
            }

            return false;
        }
    }

    /**
     * Send contact form email
     */
    public static function sendContactForm(
        string $name,
        string $email,
        string $subject,
        string $message
    ): bool {
        $to = defined('SITE_EMAIL') ? SITE_EMAIL : 'info@example.com';

        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #555; }
                .value { margin-top: 5px; padding: 10px; background-color: white; border-left: 3px solid #4CAF50; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>New Contact Form Submission</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='label'>Name:</div>
                        <div class='value'>" . htmlspecialchars($name) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Email:</div>
                        <div class='value'>" . htmlspecialchars($email) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Subject:</div>
                        <div class='value'>" . htmlspecialchars($subject) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Message:</div>
                        <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";

        return self::send($to, 'Contact Form: ' . $subject, $body);
    }

    /**
     * Send newsletter confirmation email
     */
    public static function sendNewsletterConfirmation(string $email): bool
    {
        $siteName = defined('SITE_NAME') ? SITE_NAME : 'Global Harmony Initiative';

        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Thank You for Subscribing!</h2>
                </div>
                <div class='content'>
                    <p>Dear Subscriber,</p>
                    <p>Thank you for subscribing to the {$siteName} newsletter. You will now receive updates about our initiatives, events, and impact stories.</p>
                    <p>We're excited to share our journey with you!</p>
                    <p>Best regards,<br>{$siteName} Team</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return self::send($email, sprintf('Welcome to %s Newsletter', $siteName), $body);
    }

    /**
     * Send password reset email
     */
    public static function sendPasswordReset(string $email, string $resetToken, string $resetUrl): bool
    {
        $siteName = defined('SITE_NAME') ? SITE_NAME : 'Global Harmony Initiative';

        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .button { display: inline-block; padding: 12px 24px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Password Reset Request</h2>
                </div>
                <div class='content'>
                    <p>You have requested to reset your password for your {$siteName} account.</p>
                    <p>Click the button below to reset your password:</p>
                    <p style='text-align: center;'>
                        <a href='{$resetUrl}' class='button'>Reset Password</a>
                    </p>
                    <p>If you did not request this, please ignore this email.</p>
                    <p>This link will expire in 1 hour.</p>
                    <p>Best regards,<br>{$siteName} Team</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return self::send($email, 'Password Reset - ' . $siteName, $body);
    }
}

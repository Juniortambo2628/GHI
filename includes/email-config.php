<?php
/**
 * Email Configuration
 * Uses environment detection to configure email settings
 */

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

/**
 * Get email configuration based on environment
 */
function get_email_config(): array {
    // Load environment config
    static $env_config = null;
    if ($env_config === null) {
        $env_config = get_environment_config();
    }
    
    return [
        'host' => $env_config['mail_host'],
        'port' => $env_config['mail_port'],
        'encryption' => $env_config['mail_encryption'],
        'username' => $env_config['mail_username'],
        'password' => $env_config['mail_password'],
        'from_email' => $env_config['mail_from'],
        'from_name' => $env_config['mail_from_name'],
    ];
}

/**
 * Create and configure mailer instance
 */
function create_mailer(): \Symfony\Component\Mailer\Mailer {
    $config = get_email_config();
    
    if (IS_PRODUCTION) {
        // Production: Use SMTP
        $dsn = sprintf(
            'smtp://%s:%s@%s:%d',
            urlencode((string) $config['username']),
            urlencode((string) $config['password']),
            $config['host'],
            $config['port']
        );
        
        // Add encryption if specified
        if ($config['encryption']) {
            $dsn .= '?encryption=' . $config['encryption'];
        }
    } else {
        // Development: Use sendmail or null (log only)
        $dsn = 'smtp://localhost:1025'; // MailHog or similar
    }
    
    $transport = Transport::fromDsn($dsn);
    return new Mailer($transport);
}

/**
 * Send email using existing MailService
 * This is a wrapper that uses the send_email() function from functions.php
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML or text)
 * @return bool Success status
 */
function send_ghi_email($to, $subject, $body) {
    try {
        $config = get_email_config();
        
        // Use the existing send_email() function from functions.php
        $result = send_email($to, $subject, $body, $config['from_email'], $config['from_name'], true);
        
        // Log success or failure
        if ($result) {
            log_message('info', 'Email sent successfully', [
                'to' => $to,
                'subject' => $subject,
                'environment' => ENVIRONMENT,
            ]);
        } else {
            log_message('error', 'Email sending failed', [
                'to' => $to,
                'subject' => $subject,
                'environment' => ENVIRONMENT,
            ]);
        }
        
        return $result;
        
    } catch (\Exception $exception) {
        // Log error
        log_message('error', 'Email sending exception', [
            'to' => $to,
            'subject' => $subject,
            'error' => $exception->getMessage(),
            'environment' => ENVIRONMENT,
        ]);
        
        return false;
    }
}

/**
 * Send newsletter subscription confirmation
 */
function send_newsletter_confirmation($email, $name = '') {
    $subject = 'Welcome to Global Harmony Initiative Newsletter';
    
    // Build greeting
    $greeting = 'Hi' . ($name ? ' ' . htmlspecialchars((string) $name) : '') . ',';
    
    // Get site URL
    $site_url = $GLOBALS['env_config']['site_url'] ?? SITE_URL;
    
    $body = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
            .content { padding: 30px; background: #f9f9f9; }
            .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Welcome to Our Community!</h1>
            </div>
            <div class="content">
                <p>{$greeting}</p>
                <p>Thank you for subscribing to the Global Harmony Initiative newsletter!</p>
                <p>You'll now receive updates about our latest initiatives, events, and impact stories.</p>
                <p>We're committed to bridging global compassion with local action in East Africa.</p>
                <a href="{$site_url}" class="button">Visit Our Website</a>
            </div>
            <div class="footer">
                <p>&copy; Global Harmony Initiative Inc. | U.S. Registered 501(c)(3) Nonprofit</p>
                <p><a href="{$site_url}/unsubscribe?email={$email}">Unsubscribe</a></p>
            </div>
        </div>
    </body>
    </html>
HTML;
    
    return send_ghi_email($email, $subject, $body);
}

/**
 * Send contact form submission notification to admin
 */
function send_contact_notification(array $contact_data) {
    get_email_config();
    $admin_email = SITE_EMAIL;
    
    $subject = 'New Contact Form Submission - ' . SITE_NAME;
    
    $body = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #667eea; color: white; padding: 20px; }
            .content { padding: 20px; background: #f9f9f9; }
            .field { margin: 15px 0; padding: 10px; background: white; border-left: 3px solid #667eea; }
            .label { font-weight: bold; color: #667eea; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>New Contact Form Submission</h2>
            </div>
            <div class="content">
                <div class="field">
                    <div class="label">Name:</div>
                    <div>{$contact_data['name']}</div>
                </div>
                <div class="field">
                    <div class="label">Email:</div>
                    <div>{$contact_data['email']}</div>
                </div>
                <div class="field">
                    <div class="label">Phone:</div>
                    <div>{$contact_data['phone']}</div>
                </div>
                <div class="field">
                    <div class="label">Subject:</div>
                    <div>{$contact_data['subject']}</div>
                </div>
                <div class="field">
                    <div class="label">Message:</div>
                    <div>{$contact_data['message']}</div>
                </div>
                <div class="field">
                    <div class="label">Submitted:</div>
                    <div>{$contact_data['created_at']}</div>
                </div>
            </div>
        </div>
    </body>
    </html>
HTML;
    
    return send_ghi_email($admin_email, $subject, $body);
}

/**
 * Send welcome email to new volunteer
 */
function send_volunteer_welcome($email, $name) {
    $subject = 'Welcome to the Global Harmony Initiative Family!';
    
    // Get site URL
    $site_url = $GLOBALS['env_config']['site_url'] ?? SITE_URL;
    $safe_name = htmlspecialchars((string) $name);
    
    $body = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; text-align: center; }
            .content { padding: 30px; background: #f9f9f9; }
            .button { display: inline-block; padding: 12px 30px; background: #4facfe; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Welcome Aboard, {$safe_name}!</h1>
            </div>
            <div class="content">
                <p>Thank you for your interest in volunteering with Global Harmony Initiative!</p>
                <p>We're excited to have you join our community of changemakers.</p>
                <p>We'll be in touch soon with more information about volunteer opportunities that match your interests.</p>
                <a href="{$site_url}/coming-soon-get-involved" class="button">Learn More</a>
            </div>
        </div>
    </body>
    </html>
HTML;
    
    return send_ghi_email($email, $subject, $body);
}

// Test email function for debugging
function test_email_configuration(): void {
    if (!IS_PRODUCTION) {
        $config = get_email_config();
        error_log('Email Configuration Test:');
        error_log('Host: ' . $config['host']);
        error_log('Port: ' . $config['port']);
        error_log('From: ' . $config['from_email']);
        error_log('Environment: ' . ENVIRONMENT);
    }
}


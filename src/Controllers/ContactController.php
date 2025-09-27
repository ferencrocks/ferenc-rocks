<?php
namespace App\Controllers;

use Slim\Views\PhpRenderer;
use Gregwar\Captcha\CaptchaBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class ContactController
{
    public function __construct(private PhpRenderer $renderer)
    {}

    public function show(Request $request, Response $response)
    {
        $captcha = $this->prepareCaptcha();

        return $this->renderer->render($response, 'contact.php', [
            'title' => 'Contact',
            'captcha' => $captcha->inline(),
        ]);
    }

    public function post(Request $request, Response $response)
    {
        $data = (array) ($request->getParsedBody() ?? []);

        // Sanitize
        $nameRaw = $data['name'] ?? '';
        $emailRaw = $data['email'] ?? '';
        $messageRaw = $data['message'] ?? '';
        $captchaInputRaw = $data['captcha'] ?? '';

        $name = trim(preg_replace('/\s+/u', ' ', strip_tags($nameRaw)) ?? '');
        $email = trim(filter_var($emailRaw, FILTER_SANITIZE_EMAIL));
        $message = trim(preg_replace('/\s+/u', ' ', strip_tags($messageRaw)) ?? '');
        $captchaInput = trim($captchaInputRaw);

        $errors = [];

        // Validate name
        if ($name === '') {
            $errors['name'] = 'Name is required.';
        } elseif (mb_strlen($name) > 100) {
            $errors['name'] = 'Name must be 100 characters or fewer.';
        }

        // Validate email
        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        // Validate message
        if ($message === '') {
            $errors['message'] = 'Message is required.';
        } elseif (mb_strlen($message) > 5000) {
            $errors['message'] = 'Message is too long (max 5000 characters).';
        }

        // Validate captcha
        $captchaPhrase = $_SESSION['captchaPhrase'] ?? null;
        if ($captchaInput === '') {
            $errors['captcha'] = 'Captcha is required.';
        } elseif (!$captchaPhrase || strcasecmp($captchaInput, (string)$captchaPhrase) !== 0) {
            $errors['captcha'] = 'Captcha is incorrect.';
        }

        // Prepare new captcha for re-rendering
        $captcha = $this->prepareCaptcha();

        if (!empty($errors)) {
            return $this->renderer->render($response, 'contact.php', [
                'title' => 'Contact',
                'errors' => $errors,
                'old' => [
                    'name' => htmlspecialchars($nameRaw, ENT_QUOTES, 'UTF-8'),
                    'email' => htmlspecialchars($emailRaw, ENT_QUOTES, 'UTF-8'),
                    'message' => htmlspecialchars($messageRaw, ENT_QUOTES, 'UTF-8'),
                ],
                'captcha' => $captcha->inline(),
            ]);
        }

        // Send email via PHPMailer SMTP
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'] ?? 'localhost';
            $mail->Port = (int)($_ENV['SMTP_PORT'] ?? 25);
            $secure = strtolower((string)($_ENV['SMTP_SECURE'] ?? ''));
            if ($secure === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($secure === 'tls' || $secure === 'starttls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->SMTPAuth = (bool)($_ENV['SMTP_AUTH'] ?? true);
            if (!empty($_ENV['SMTP_USERNAME'])) {
                $mail->Username = $_ENV['SMTP_USERNAME'];
            }
            if (!empty($_ENV['SMTP_PASSWORD'])) {
                $mail->Password = $_ENV['SMTP_PASSWORD'];
            }

            $from = $_ENV['MAIL_FROM'] ?? 'no-reply@example.com';
            $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'Website';
            $to = $_ENV['MAIL_TO'] ?? $from;

            $mail->CharSet = 'UTF-8';
            $mail->setFrom($from, $fromName);
            $mail->addAddress($to);
            // Let recipient reply directly to the user's email
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($email, $name ?: $email);
            }

            $mail->Subject = '[Contact] New message from ' . $name;
            // Build a safe plain-text body
            $bodyLines = [
                'You received a new contact message:',
                'Name: ' . $name,
                'Email: ' . $email . '<br>',
                'Message:',
                $message,
            ];
            $mail->Body = implode("<br>", $bodyLines);
            $mail->AltBody = $mail->Body;

            // Avoid HTML content to keep it simple and safe
            $mail->isHTML(true);

            $mail->send();

            // On success, show success message and blank form
            return $this->renderer->render($response, 'contact.php', [
                'title' => 'Contact',
                'success' => 'Thank you! Your message has been sent successfully.',
                'captcha' => $captcha->inline(),
            ]);
        } catch (MailException $e) {
            $errors['general'] = 'Failed to send your message. Please try again later.';

            return $this->renderer->render($response, 'contact.php', [
                'title' => 'Contact',
                'errors' => $errors,
                'old' => [
                    'name' => htmlspecialchars($nameRaw, ENT_QUOTES, 'UTF-8'),
                    'email' => htmlspecialchars($emailRaw, ENT_QUOTES, 'UTF-8'),
                    'message' => htmlspecialchars($messageRaw, ENT_QUOTES, 'UTF-8'),
                ],
                'captcha' => $captcha->inline(),
            ]);
        }
    }

    private function prepareCaptcha(): CaptchaBuilder
    {
        $captcha = new CaptchaBuilder();
        $captcha->build();
        $_SESSION['captchaPhrase'] = $captcha->getPhrase();

        return $captcha;
    }
}
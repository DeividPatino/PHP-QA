<?php

declare(strict_types=1);

namespace App\Infrastructure\Email;

use App\Application\User\Port\EmailServicePort;
use App\Domain\User\Entity\UserModel;

final class EmailService implements EmailServicePort
{
    public function __construct(private readonly ?string $logPath = null)
    {
    }

    public function sendWelcomeEmail(UserModel $user): void
    {
        $to = $user->email()->value();
        $subject = 'Bienvenido a la plataforma';
        $message = sprintf(
            "Hola %s, tu usuario fue creado correctamente.",
            $user->name()->value()
        );

        // Para entornos locales de estudio usamos un fallback a log.
        $sent = @mail($to, $subject, $message);

        if ($sent) {
            return;
        }

        $target = $this->logPath ?? (__DIR__ . '/../../../var/email.log');
        $dir = dirname($target);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $entry = sprintf(
            "[%s] TO:%s | SUBJECT:%s | MESSAGE:%s%s",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            $message,
            PHP_EOL
        );

        file_put_contents($target, $entry, FILE_APPEND);
    }
}

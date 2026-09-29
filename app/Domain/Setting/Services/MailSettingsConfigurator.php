<?php

namespace App\Domain\Setting\Services;

use App\Domain\Setting\Settings\MailSettings;
use Illuminate\Support\Str;

class MailSettingsConfigurator
{
    public function registerMailer(?string $mailerName = null): string
    {
        $mail = app(MailSettings::class);
        $name = $mailerName ?? 'settings_' . Str::random(8);

        config([
            "mail.mailers.{$name}" => [
                'transport' => $mail->driver ?: 'log',
                'host' => $mail->host,
                'port' => $mail->port,
                'encryption' => $mail->encryption,
                'username' => $mail->username,
                'password' => $mail->password,
                'timeout' => null,
            ],
            'mail.from.address' => $mail->from_address ?: config('mail.from.address'),
            'mail.from.name' => $mail->from_name ?: config('mail.from.name'),
        ]);

        return $name;
    }
}

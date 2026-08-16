<?php

namespace App\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class SanitizeLogProcessor implements ProcessorInterface
{
    /**
     * Keys whose values must be masked in log records.
     */
    protected array $sensitiveKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'remember_token',
        'secret',
        'authorization',
        'cookie',
        'api_key',
        'apikey',
        'access_token',
        'refresh_token',
        'cvv',
        'card_number',
        'nomor_kartu',
        'nik',
        'no_hp',
    ];

    /**
     * Process and sanitize log record context and extra data.
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $context = $this->sanitize($record->context);
        $extra = $this->sanitize($record->extra);

        return $record->with(
            context: $context,
            extra: $extra
        );
    }

    /**
     * Recursively mask sensitive keys in arrays.
     */
    protected function sanitize(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        $sanitized = [];

        foreach ($data as $key => $value) {
            $isSensitive = false;
            $lowerKey = strtolower((string) $key);

            foreach ($this->sensitiveKeys as $sensitiveKey) {
                if (str_contains($lowerKey, $sensitiveKey)) {
                    $isSensitive = true;
                    break;
                }
            }

            if ($isSensitive) {
                $sanitized[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitize($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}

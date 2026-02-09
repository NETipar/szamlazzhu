<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Support;

use Illuminate\Support\Facades\Log;

trait HasLogging
{
    protected function log(string $message, string $level = 'debug'): void
    {
        $logChannel = $this->logChannel();

        if ($logChannel !== null) {
            Log::channel($logChannel)->{$level}("[Szamlazzhu] {$message}");
        }
    }

    abstract protected function logChannel(): ?string;
}

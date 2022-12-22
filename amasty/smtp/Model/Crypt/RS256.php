<?php

declare(strict_types=1);

namespace Amasty\Smtp\Model\Crypt;

class RS256
{
    public function sign(string $message, $key): string
    {
        openssl_sign($message, $signature, $key, OPENSSL_ALGO_SHA256);

        return $signature;
    }
}

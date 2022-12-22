<?php

namespace Amasty\Smtp\Model\Token\Generator;

interface AuthTokenGeneratorInterface
{
    public const TOKEN_LIFETIME = 3600;

    public function generateToken(): string;
}

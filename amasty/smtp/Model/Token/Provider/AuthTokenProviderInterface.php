<?php

namespace Amasty\Smtp\Model\Token\Provider;

interface AuthTokenProviderInterface
{
    public function execute(): string;
}

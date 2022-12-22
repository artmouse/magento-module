<?php

declare(strict_types=1);

namespace Amasty\Smtp\Model\Provider;

interface ConnectionProviderInterface
{
    public function getConnection(\Laminas\Mail\Transport\SmtpOptions $options): \Laminas\Mail\Protocol\Smtp;
}

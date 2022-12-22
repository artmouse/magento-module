<?php

declare(strict_types=1);

namespace Amasty\Smtp\Model\Serialize\Serializer;

class Base64UrlEncoder
{
    public function execute($data)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($data)
        );
    }
}

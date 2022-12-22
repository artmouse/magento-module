<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\Formatter;

use Magento\Framework\Phrase;

class CommonOutput
{
    public const ERROR_KEY = 'error';
    public const MESSAGE_KEY = 'message';

    public function format(Phrase $message, bool $isError = false): array
    {
        return [
            self::ERROR_KEY => $isError,
            self::MESSAGE_KEY => $message
        ];
    }
}

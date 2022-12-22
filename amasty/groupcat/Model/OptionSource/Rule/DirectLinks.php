<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Catalog for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Groupcat\Model\OptionSource\Rule;

use Magento\Framework\Data\OptionSourceInterface;

class DirectLinks implements OptionSourceInterface
{
    public const DENY = 0;
    public const ALLOW = 1;

    public function toOptionArray(): array
    {
        return [
            ['value' => self::DENY, 'label' => __('Deny')],
            ['value' => self::ALLOW, 'label' => __('Allow')]
        ];
    }
}

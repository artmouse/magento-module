<?php

namespace Amasty\Paction\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TierPrice implements OptionSourceInterface
{
    public const VALUE_FIXED = 'fixed';
    public const VALUE_PERCENT = 'percent';

    public function toOptionArray()
    {
        return [
            ['value' => self::VALUE_FIXED, 'label' => __('Fixed')],
            ['value' => self::VALUE_PERCENT, 'label' => __('Discount')],
        ];
    }
}

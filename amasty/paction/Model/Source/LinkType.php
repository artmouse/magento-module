<?php
declare(strict_types=1);

namespace Amasty\Paction\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LinkType implements OptionSourceInterface
{
    public const DEFAULT = 0;
    public const TWO_WAY = 1;
    public const MULTI_WAY = 2;

    public function toOptionArray()
    {
        $result = [];

        foreach ($this->toArray() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $result;
    }

    public function toArray(): array
    {
        return [
            self::DEFAULT => __('Default'),
            self::TWO_WAY => __('2 Way'),
            self::MULTI_WAY => __('Multi Way')
        ];
    }
}

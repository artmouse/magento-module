<?php
declare(strict_types=1);

namespace Amasty\Paction\Model\Source;

class Rounding implements \Magento\Framework\Option\ArrayInterface
{
    public const FIXED = 'fixed';
    public const MATH = 'math';
    public const CROP = 'crop';
    public const NEAREST_INT = 'nearest_int';

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
            self::FIXED => __('To specific value'),
            self::MATH  => __('Rounding to two decimal places'),
            self::CROP => __('Truncate to two decimal places without rounding'),
            self::NEAREST_INT => __('Rounding to the nearest integer')
        ];
    }
}

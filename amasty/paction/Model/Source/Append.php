<?php
declare(strict_types=1);

namespace Amasty\Paction\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Append implements OptionSourceInterface
{
    public const POSITION_BEFORE = 'before';
    public const POSITION_AFTER = 'after';

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
            self::POSITION_BEFORE => __('Before Attribute Text'),
            self::POSITION_AFTER => __('After Attribute Text')
        ];
    }
}

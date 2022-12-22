<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Product Labels for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Label\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public const ACTIVE = 1;
    public const INACTIVE = 0;

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::INACTIVE,
                'label' => __('Inactive')
            ],
            [
                'value' => self::ACTIVE,
                'label' => __('Active')
            ]
        ];
    }
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Catalog for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Groupcat\Model\OptionSource\Rule;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public const INACTIVE = 0;
    public const ACTIVE = 1;

    public function toOptionArray(): array
    {
        return [
            ['value' => self::ACTIVE, 'label' => __('Active')],
            ['value' => self::INACTIVE, 'label' => __('Inactive')]
        ];
    }
}

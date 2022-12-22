<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Amp\Model\Config\Source;

class Accelerated implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('From AMP Google results only')],
            ['value' => '1', 'label' => __('Always')]
        ];
    }
}

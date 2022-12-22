<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/

namespace Amasty\Cart\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class Configurable implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => '0',
                'label' =>__('Parent Configurable Product Image')
            ],
            [
                'value' => '1',
                'label' =>__('Child Simple Product Image')
            ]
        ];

        return $options;
    }
}

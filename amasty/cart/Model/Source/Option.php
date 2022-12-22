<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/
namespace Amasty\Cart\Model\Source;

class Option implements \Magento\Framework\Option\ArrayInterface
{
    public const ONLY_REQUIRED = '0';
    public const ALL_OPTIONS   = '1';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ONLY_REQUIRED,
                'label' => __('Show Only if There Are Required Options')
            ],
            [
                'value' => self::ALL_OPTIONS,
                'label' => __('Always Show All Custom Options')
            ]
        ];

        return $options;
    }
}

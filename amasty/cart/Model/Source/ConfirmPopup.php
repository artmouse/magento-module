<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/

namespace Amasty\Cart\Model\Source;

class ConfirmPopup implements \Magento\Framework\Option\ArrayInterface
{
    public const MINI_PAGE = '0';
    public const OPTIONS = '1';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::MINI_PAGE,
                'label' => __('Mini Product Page')
            ],
            [
                'value' => self::OPTIONS,
                'label' => __('Custom Options & Product Qty')
            ]
        ];

        return $options;
    }
}

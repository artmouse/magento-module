<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Improved Sorting for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Sorting\Model\Source;

class CategorySortAfter extends AllSortingAttributes
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = parent::toOptionArray();
        array_unshift($options, [
            'value' => '',
            'label' => __('--Please Select--')
        ]);

        return $options;
    }
}

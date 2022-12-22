<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products for Magento 2
*/

namespace Amasty\Mostviewed\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Framework\Phrase;

class Combine extends \Magento\CatalogRule\Model\Rule\Condition\Combine
{
    private const SORTING_CONDITIONS = 'amasty_sorting';

    /**
     * @return mixed
     */
    public function getWhereConditions()
    {
        return $this->getData('conditions');
    }

    public function getNewChildSelectOptions(): array
    {
        $conditions = AbstractCondition::getNewChildSelectOptions();
        $conditions[] = ['label' => __('Conditions Combination'), 'value' => static::class];
        $conditions[] = ['label' => __('Product Attribute'), 'value' => $this->getProductConditions()];
        $conditions[] = [
            'label' => $this->getSortingConditionsLabel(),
            'value' => $this->isSortingEnabled() ? $this->getSortingConditions() : []
        ];

        return $conditions;
    }

    private function getSortingConditions(): array
    {
        $sortingConditions = (array) $this->getData(self::SORTING_CONDITIONS);

        return array_reduce($sortingConditions, [$this, 'formatCondition'], []);
    }

    /**
     * Formation of an array of conditions in the required format
     *
     * @param array $carry
     * @param AbstractCondition $condition
     * @return array
     */
    private function formatCondition(array $carry, AbstractCondition $condition): array
    {
        $carry[] = [
            'label' => $condition->getAttributeElementHtml(),
            'value' => ltrim(get_class($condition), '/')
        ];

        return $carry;
    }

    private function getSortingConditionsLabel(): Phrase
    {
        $title = __('Improved Sorting (not installed)');

        if ($this->isSortingEnabled()) {
            $title = __('Improved Sorting');
        }

        return $title;
    }

    private function isSortingEnabled(): bool
    {
        return $this->getData('module_manager') && $this->getData('module_manager')->isEnabled('Amasty_Sorting');
    }

    private function getProductConditions(): array
    {
        $productAttributes = $this->_productFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes = [];

        foreach ($productAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Magento\CatalogRule\Model\Rule\Condition\Product|' . $code,
                'label' => $label,
            ];
        }

        return $attributes;
    }
}

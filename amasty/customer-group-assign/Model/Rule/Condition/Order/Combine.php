<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/
namespace Amasty\GroupAssign\Model\Rule\Condition\Order;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Rule\Model\Condition\Context;
use Magento\Rule\Model\Condition\Combine as CombineRule;
use Amasty\GroupAssign\Model\Rule\Condition\Order\Combine as CombineOrder;
use Magento\Framework\Model\AbstractModel;
use Magento\SalesRule\Model\Rule\Condition\Product as ProductCondition;
use Magento\Sales\Model\Order;

class Combine extends CombineRule
{
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType(CombineOrder::class);
    }

    /**
     * Collect validated attributes
     *
     * @param Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }

    /**
     * Is entity valid
     *
     * @param int|AbstractModel $entity
     * @return bool
     */
    protected function _isValid($entity)
    {
        if (!$this->getConditions()) {
            return true;
        }

        $all = $this->getAggregator() === 'all';
        $true = (bool)$this->getValue();
        $validated = false;

        foreach ($this->getConditions() as $cond) {
            if ($cond instanceof ProductCondition) {
                if ($entity instanceof Order) {
                    $allItems = $entity->getAllVisibleItems();
                    $validated = false;

                    foreach ($allItems as $item) {
                        //Magento\ConfigurableProduct\Plugin\SalesRule\Model\Rule\Condition\Product workaround
                        $item->setChildren($item->getChildrenItems());

                        if ($item->getProduct() && $cond->validate($item)) {
                            $validated = true;

                            break;
                        }
                    }
                } elseif ($entity->getProduct()) {
                    $validated = $cond->validate($entity);
                }
            } else {
                if ($entity instanceof AbstractModel) {
                    $validated = $cond->validate($entity);
                } else {
                    $validated = $cond->validateByEntityId($entity);
                }
            }

            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }

        return $all ? true : false;
    }
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Model\Rule\Condition\Order\Subselect;

use Magento\Rule\Model\Condition\Context;
use Magento\SalesRule\Model\Rule\Condition\ProductFactory as ConditionProductFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Customer\Model\Customer;
use Amasty\GroupAssign\Model\Rule\Condition\Order\Subselect;
use Amasty\GroupAssign\Model\Rule\Condition\Order\Subselect\Ordered as SubselectOrdered;
use Magento\SalesRule\Model\Rule\Condition\Product as ProductCondition;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

class Ordered extends Subselect
{
    /**
     * @var ConditionProductFactory
     */
    private $conditionProductFactory;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var array
     */
    private $restrictProductAttributes = ['quote_item_price', 'quote_item_qty', 'quote_item_row_total'];

    public function __construct(
        Context $context,
        ConditionProductFactory $conditionProductFactory,
        OrderCollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType(SubselectOrdered::class)->setValue(null);
        $this->conditionProductFactory = $conditionProductFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(['qty' => __('total quantity')]);

        return $this;
    }

    /**
     * Return as html
     *
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml()
            . __(
                "If %1 %2 %3 for a subselection of products for orders matching %4 of these conditions:",
                $this->getAttributeElement()->getHtml(),
                $this->getOperatorElement()->getHtml(),
                $this->getValueElement()->getHtml(),
                $this->getAggregatorElement()->getHtml()
            );

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        /** @var ProductCondition $productCondition */
        $productCondition = $this->conditionProductFactory->create();
        $productConditionAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        $productAttributes = [];
        foreach ($productConditionAttributes as $code => $label) {
            if (in_array($code, $this->restrictProductAttributes)) {
                continue;
            }
            $productAttributes[] = [
                'value' => ProductCondition::class . '|' . $code,
                'label' => $label,
            ];
        }

        $conditions = array_merge_recursive(
            parent::getNewChildSelectOptions(),
            [
                ['label' => __('Product Attribute'), 'value' => $productAttributes],
            ]
        );

        return $conditions;
    }

    /**
     * Validate
     *
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        if ($model instanceof Customer) {
            $orders = $this->getOrdersCollectionByCustomerId($model->getId());

            if ($orders && count($orders)) {
                $total = 0;
                foreach ($orders as $order) {
                    $total += $this->getValidItemsTotal($order);
                }

                return $this->validateAttribute($total);
            }
        }

        return false;
    }

    /**
     * @param int $customerId
     * @return OrderCollection|bool
     */
    private function getOrdersCollectionByCustomerId(int $customerId)
    {
        $orderCollection = $this->orderCollectionFactory
            ->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('customer_id', ['eq' => $customerId]);

        return $orderCollection->getSize() ? $orderCollection : false;
    }
}

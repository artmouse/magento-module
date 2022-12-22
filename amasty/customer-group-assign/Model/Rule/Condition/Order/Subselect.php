<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Model\Rule\Condition\Order;

use Magento\Rule\Model\Condition\Context;
use Amasty\GroupAssign\Model\Rule\Condition\Order\Subselect as SubselectCondition;
use Magento\Sales\Api\Data\OrderInterface;

class Subselect extends Combine
{
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType(SubselectCondition::class)->setValue(null);
    }

    /**
     * Load array
     *
     * @param array $arr
     * @param string $key
     * @return $this
     */
    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);

        return $this;
    }

    /**
     * Return as xml
     *
     * @param string $containerKey
     * @param string $itemKey
     * @return string
     */
    public function asXml($containerKey = 'conditions', $itemKey = 'condition')
    {
        $xml = '<attribute>' . $this->getAttribute() . '</attribute>'
            . '<operator>' . $this->getOperator() . '</operator>'
            . parent::asXml($containerKey, $itemKey);

        return $xml;
    }

    /**
     * Load value options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        return $this;
    }

    /**
     * Load operator options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '==' => __('is'),
                '>=' => __('equals or greater than'),
                '<=' => __('equals or less than'),
                '>' => __('greater than'),
                '<' => __('less than'),
            ]
        );

        return $this;
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * @param OrderInterface $order
     * @return int
     */
    protected function getValidItemsTotal($order)
    {
        $total = 0;

        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($order->getAllVisibleItems() as $item) {
            try {
                if (!$this->getConditions() || parent::validate($item)) {
                    $total += $item->getQtyInvoiced();
                }
            } catch (\Exception $e) { //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
                // Skip if the requested item has been deleted
            }
        }

        return $total;
    }
}

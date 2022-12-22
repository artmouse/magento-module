<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Model\Rule\Condition\Customer;

use Amasty\GroupAssign\Model\Rule\Condition\Address\BillingFactory;
use Amasty\GroupAssign\Model\Rule\Condition\Address\ShippingFactory;
use Amasty\GroupAssign\Model\Rule\Condition\Customer;
use Amasty\GroupAssign\Model\Rule\Condition\CustomerFactory;
use Amasty\GroupAssign\Model\Rule\Condition\OrderFactory;
use Magento\Customer\Model\ResourceModel\Customer\Collection;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var CustomerFactory
     */
    private $conditionCustomerFactory;

    /**
     * @var OrderFactory
     */
    private $conditionOrderFactory;

    /**
     * @var BillingFactory
     */
    private $conditionBillingFactory;

    /**
     * @var ShippingFactory
     */
    private $conditionShippingFactory;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        CustomerFactory $conditionCustomerFactory,
        OrderFactory $conditionOrderFactory,
        BillingFactory $conditionBillingFactory,
        ShippingFactory $conditionShippingFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->conditionCustomerFactory = $conditionCustomerFactory;
        $this->setType(Combine::class);
        $this->conditionOrderFactory = $conditionOrderFactory;
        $this->conditionBillingFactory = $conditionBillingFactory;
        $this->conditionShippingFactory = $conditionShippingFactory;
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $options = parent::getNewChildSelectOptions();

        /** @var Customer $condition */
        $conditionCustomer = $this->conditionCustomerFactory->create();
        $conditionCustomerAttributes = $conditionCustomer->loadAttributeOptions()->getAttributeOption();

        /** @var \Amasty\GroupAssign\Model\Rule\Condition\Order $conditionOrder */
        $conditionOrder = $this->conditionOrderFactory->create();
        $conditionOrderAttributes = $conditionOrder->loadAttributeOptions()->getAttributeOption();

        /** @var \Amasty\GroupAssign\Model\Rule\Condition\Address\Billing $conditionBilling */
        $conditionBilling = $this->conditionBillingFactory->create();
        $conditionBillingAttributes = $conditionBilling->loadAttributeOptions()->getAttributeOption();

        /** @var \Amasty\GroupAssign\Model\Rule\Condition\Address\Shipping $conditionShipping */
        $conditionShipping = $this->conditionShippingFactory->create();
        $conditionShippingAttributes = $conditionShipping->loadAttributeOptions()->getAttributeOption();

        $customerAttributes = [];
        $orderAttributes = [];
        $billingAttributes = [];
        $shippingAttributes = [];

        foreach ($conditionCustomerAttributes as $code => $label) {
            if ($code == 'lock_expires') {
                $label = 'Lock Expire';
            }
            $customerAttributes[] = [
                'value' => Customer::class . '|' . $code,
                'label' => $label,
            ];
        }

        foreach ($conditionOrderAttributes as $code => $label) {
            $orderAttributes[] = [
                'value' => \Amasty\GroupAssign\Model\Rule\Condition\Order::class . '|' . $code,
                'label' => $label
            ];
        }

        foreach ($conditionBillingAttributes as $code => $label) {
            $billingAttributes[] = [
                'value' => \Amasty\GroupAssign\Model\Rule\Condition\Address\Billing::class . '|' . $code,
                'label' => $label
            ];
        }

        foreach ($conditionShippingAttributes as $code => $label) {
            $shippingAttributes[] = [
                'value' => \Amasty\GroupAssign\Model\Rule\Condition\Address\Shipping::class . '|' . $code,
                'label' => $label
            ];
        }
        $orderAttributes[] = [
            'value' => \Amasty\GroupAssign\Model\Rule\Condition\Order\Subselect\Ordered::class,
            'label' => __('Ordered Products By Condition')
        ];

        $options[] = [
            'value' => \Amasty\GroupAssign\Model\Rule\Condition\Customer\Combine::class,
            'label' => __('Conditions Combination'),
        ];
        $options[] = [
            'value' => $customerAttributes,
            'label' => __('Customer attributes'),
        ];
        $options[] = [
            'value' => $orderAttributes,
            'label' => __('Order attributes')
        ];
        $options[] = [
            'value' => $billingAttributes,
            'label' => __('Billing Address')
        ];
        $options[] = [
            'value' => $shippingAttributes,
            'label' => __('Shipping Address')
        ];

        return $options;
    }

    /**
     * @param Collection $collection
     * @return $this
     */
    public function collectValidatedAttributes($collection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Customer $condition */
            $condition->collectValidatedAttributes($collection);
        }

        return $this;
    }
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/
declare(strict_types=1);

namespace Amasty\GroupAssign\Model\Rule\Condition;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Customer\Model\Customer;
use Magento\Directory\Model\Config\Source\Allregion;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

class Address extends AbstractCondition
{
    private const IS_UNDEFINED_OPERATOR = '<=>';

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var Country
     */
    private $directoryCountry;

    /**
     * @var Allregion
     */
    private $directoryAllregion;

    public function __construct(
        Context $context,
        Country $directoryCountry,
        Allregion $directoryAllregion,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->directoryCountry = $directoryCountry;
        $this->directoryAllregion = $directoryAllregion;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            AddressInterface::POSTCODE => __('Shipping Postcode'),
            AddressInterface::COUNTRY_ID => __('Shipping Country'),
            AddressInterface::REGION_ID => __('Shipping State/Province'),
            AddressInterface::CITY => __('Shipping City'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getInputType(): string
    {
        switch ($this->getAttribute()) {
            case AddressInterface::POSTCODE:
                return 'numeric';
            case AddressInterface::COUNTRY_ID:
            case AddressInterface::REGION_ID:
                return 'select';
        }

        return 'string';
    }

    public function getValueElementType(): string
    {
        switch ($this->getAttribute()) {
            case AddressInterface::COUNTRY_ID:
            case AddressInterface::REGION_ID:
                return 'select';
        }

        return 'text';
    }

    /**
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case AddressInterface::COUNTRY_ID:
                    $options = $this->directoryCountry->toOptionArray();
                    break;

                case AddressInterface::REGION_ID:
                    $options = $this->directoryAllregion->toOptionArray();
                    break;

                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    public function getDefaultOperatorInputByType(): array
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['string'] = ['==', '!=', '{}', '!{}', '<=>', '()', '!()'];
            $this->_defaultOperatorInputByType['numeric'] = ['==', '!=', '{}', '!{}'];
        }

        return $this->_defaultOperatorInputByType;
    }

    public function validate(AbstractModel $model): bool
    {
        $isValid = false;

        if ($model instanceof Customer) {
            $address = $this->getAddress($model);

            if ($address instanceof AbstractModel) {
                $isValid = parent::validate($address);
            } elseif (!$address) {
                /**
                 * If customer doesn't have default address, then validate all addresses.
                 * If one of the all addresses will be valid, then customer is valid.
                 */
                foreach ($model->getAddresses() as $address) {
                    if (parent::validate($address)) {
                        $isValid = true;
                        break;
                    }
                }

                $isValid = $isValid ?: ($this->getOperator() === self::IS_UNDEFINED_OPERATOR);
            } else {
                $isValid = $this->validateAttribute($address);
            }

            $model->getAddressesCollection()->clear();
        }

        return $isValid;
    }

    /**
     * @return false|\Magento\Customer\Model\Address|\Magento\Customer\Model\Customer
     */
    protected function getAddress(Customer $customer)
    {
        switch ($this->type) {
            case AddressHelper::TYPE_BILLING:
                return $customer->getDefaultBillingAddress();
            case AddressHelper::TYPE_SHIPPING:
                return $customer->getDefaultShippingAddress();
        }

        return $customer;
    }

    public function validateAttribute($validatedValue): bool
    {
        if ($this->getOperator() === self::IS_UNDEFINED_OPERATOR) {
            return $validatedValue === null;
        }

        return parent::validateAttribute($validatedValue);
    }
}

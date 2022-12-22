<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/
declare(strict_types=1);

namespace Amasty\GroupAssign\Model\Rule\Condition\Address;

use Amasty\GroupAssign\Model\Rule\Condition\Address;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Model\AbstractModel;

class Billing extends Address
{
    /**
     * @var string
     */
    protected $type = 'billing';

    public function loadAttributeOptions()
    {
        $attributes = [
            CustomerInterface::EMAIL => __('Email'),
            AddressInterface::POSTCODE => __('Billing Postcode'),
            AddressInterface::COUNTRY_ID => __('Billing Country'),
            AddressInterface::REGION_ID => __('Billing State/Province'),
            AddressInterface::CITY => __('Billing City'),
            AddressInterface::COMPANY => __('Billing Company'),
            AddressInterface::VAT_ID => __('Billing Vat Number'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function validate(AbstractModel $model): bool
    {
        if ($model instanceof Customer) {
            if ($this->getAttribute() === CustomerInterface::EMAIL) {
                return $this->validateAttribute($model->getEmail());
            }

            return parent::validate($model);
        }

        return false;
    }
}

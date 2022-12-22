<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Store Pickup with Locator for Magento 2
*/

namespace Amasty\StorePickupWithLocator\Model\Quote;

use Magento\Customer\Model\Address\AbstractAddress;
use Amasty\StorePickupWithLocator\Model\Carrier\Shipping;

/**
 * Class Address for fill empty fields
 */
class QuoteAddressResolver
{
    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function fillEmpty(\Magento\Quote\Model\Quote\Address $address)
    {
        if ($address->getAddressType() == AbstractAddress::TYPE_SHIPPING
            && $address->getShippingMethod() == Shipping::SHIPPING_NAME
        ) {
            $address->setFirstname('-');
            $address->setLastname('-');
        }
    }
}

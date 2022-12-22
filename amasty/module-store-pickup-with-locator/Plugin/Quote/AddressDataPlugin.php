<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Store Pickup with Locator for Magento 2
*/

namespace Amasty\StorePickupWithLocator\Plugin\Quote;

use Amasty\StorePickupWithLocator\Model\Quote\QuoteAddressResolver;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;

/**
 * Plugin for fill empty data, data will be equals with frontend
 * in file Amasty/StorePickupWithLocator/view/frontend/web/js/model/klarna-mixin.js
 */
class AddressDataPlugin
{
    /**
     * @var QuoteAddressResolver
     */
    private $quoteAddressResolver;

    public function __construct(
        QuoteAddressResolver $quoteAddressResolver
    ) {
        $this->quoteAddressResolver = $quoteAddressResolver;
    }

    /**
     * @param ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @return array
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        foreach ([$addressInformation->getShippingAddress(), $addressInformation->getBillingAddress()] as $address) {
            $this->quoteAddressResolver->fillEmpty($address);
        }

        return [$cartId, $addressInformation];
    }
}

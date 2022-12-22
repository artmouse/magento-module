<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Store Pickup with Locator for Magento 2
*/

namespace Amasty\StorePickupWithLocator\Plugin\Quote\Model;

use Amasty\StorePickupWithLocator\Model\Carrier\Shipping;
use Magento\Framework\Module\Manager;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Model\Order\Address;

class QuoteManagementPlugin
{
    public const BRAINTREE_PAYPAL_CODE = 'braintree_paypal';

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Manager $moduleManager
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param QuoteManagement $subject
     * @param $cartId
     * @param PaymentInterface|null $paymentMethod
     */
    public function beforePlaceOrder(
        QuoteManagement $subject,
        $cartId,
        PaymentInterface $paymentMethod = null
    ) {
        $quote = $this->quoteRepository->getActive($cartId);
        if ($this->moduleManager->isEnabled('Magento_Braintree')
            && $quote->getPayment()->getMethod() == self::BRAINTREE_PAYPAL_CODE
            && $quote->getShippingAddress()->getShippingMethod() == Shipping::SHIPPING_NAME
        ) {
            $shipping = $quote->getShippingAddress();
            $cloneShippingObject = clone $shipping;
            $cloneShippingObject->setId(null);
            $cloneShippingObject->setAddressType(Address::TYPE_BILLING);
            $quote->setBillingAddress($cloneShippingObject);
            $quote->setDataChanges(true);
        }
    }
}

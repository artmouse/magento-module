<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package One Step Checkout Gift Wrap for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\CheckoutGiftWrap\Api;

interface GiftWrapInformationManagementInterface
{
    /**
     * Calculate quote totals based on quote and fee
     *
     * @param string $cartId
     * @param bool $checked
     *
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function update($cartId, $checked);
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products MFTF 3 for Magento 2 (System)
*/

namespace Amasty\MostviewedMFTF3\Test\Mftf\Helper;

use Magento\FunctionalTestingFramework\Helper\Helper;

class CalculateBundlePrice extends Helper
{
    /**
     * Calculate Bundle Item Price
     *
     * @param string $discount_type
     * @param int $product_price
     * @param int $discount_amount
     * @return int
     */
    public function getBundleItemPriceFixedDiscount (string $discount_type, int $product_price, int $discount_amount): int
    {
        if (strcasecmp($discount_type, "Fixed") == 0)
        {
            return ($product_price - $discount_amount);
        }
    }
}

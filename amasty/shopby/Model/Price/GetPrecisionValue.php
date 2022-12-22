<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Improved Layered Navigation Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Shopby\Model\Price;

use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class GetPrecisionValue
{
    private const ZERO_PRECISION = 0;

    /**
     * @param FilterSettingInterface $settings
     * @param float $price
     * @return int
     */
    public function execute(FilterSettingInterface $settings, float $price): int
    {
        return $settings->getHideZeros() && (int)$price == $price
            ? self::ZERO_PRECISION
            : PriceCurrencyInterface::DEFAULT_PRECISION;
    }
}

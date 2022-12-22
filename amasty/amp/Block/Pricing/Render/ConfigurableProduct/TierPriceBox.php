<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

namespace Amasty\Amp\Block\Pricing\Render\ConfigurableProduct;

use Amasty\Amp\Model\UrlConfigProvider;

class TierPriceBox extends \Magento\ConfigurableProduct\Pricing\Render\TierPriceBox
{
    /**
     * @return string[]
     */
    public function getCacheKeyInfo()
    {
        return [$this->getNameInLayout(), UrlConfigProvider::AMP];
    }
}

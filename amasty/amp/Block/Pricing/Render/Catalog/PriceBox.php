<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

namespace Amasty\Amp\Block\Pricing\Render\Catalog;

use Amasty\Amp\Model\UrlConfigProvider;

class PriceBox extends \Magento\Catalog\Pricing\Render\PriceBox
{
    /**
     * @return string[]
     */
    public function getCacheKeyInfo()
    {
        return [$this->getNameInLayout(), UrlConfigProvider::AMP];
    }
}

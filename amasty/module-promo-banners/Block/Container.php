<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Promo Banners Base for Magento 2
*/
declare(strict_types=1);

namespace Amasty\PromoBanners\Block;

use Amasty\PromoBanners\Model\Rule;
use Magento\Framework\View\Element\Template;

class Container extends Template
{
    public function isVisible(): bool
    {
        return $this->getPosition() != Rule::POS_AMONG_PRODUCTS;
    }
}

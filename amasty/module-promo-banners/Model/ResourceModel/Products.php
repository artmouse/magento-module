<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Promo Banners Base for Magento 2
*/

namespace Amasty\PromoBanners\Model\ResourceModel;

class Products extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_banner_rule_products', 'id');
    }
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

namespace Amasty\Amp\Block\Category\Product\ProductList\Toolbar;

class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Amp::category/product/list/toolbar/pager.phtml';

    /**
     * @param string $page
     * @return string
     */
    public function getPageUrl($page)
    {
        return $this->getData('urlConfig')->convertToAmpUrl(parent::getPageUrl($page));
    }
}

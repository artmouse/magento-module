<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

namespace Amasty\Amp\Block\Product\Content;

class Link extends \Magento\Wishlist\Block\Catalog\Product\View\AddTo\Wishlist
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Amp::product/content/wishlist_link.phtml';

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWishlistUrl()
    {
        return str_replace(['https:', 'http:'], '', $this->_storeManager->getStore()->getUrl('wishlist/index/add'));
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->getProduct()->getEntityId();
    }
}

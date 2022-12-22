<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/

namespace Amasty\Cart\Plugin\DataPost\Block\Product;

use Amasty\Cart\Plugin\DataPost\Replacer;
use Magento\Wishlist\Block\Catalog\Product\View\AddTo\Wishlist as ProductWishlist;

class Wishlist extends Replacer
{
    /**
     * @param ProductWishlist $subject
     * @param string $result
     *
     * @return string
     */
    public function afterToHtml(ProductWishlist $subject, $result)
    {
        if ($this->helper->isWishlistAjax()) {
            $this->dataPostReplace($result);
        }

        return $result;
    }
}

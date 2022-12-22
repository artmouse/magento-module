<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/

namespace Amasty\Cart\Plugin\DataPost\Block\Category;

use Amasty\Cart\Plugin\DataPost\Replacer;
use Magento\Catalog\Block\Product\ProductList\Item\AddTo\Compare as CategoryCompare;

class Compare extends Replacer
{
    /**
     * @param CategoryCompare $subject
     * @param string $result
     *
     * @return string
     */
    public function afterToHtml(CategoryCompare $subject, $result)
    {
        if ($this->helper->isCompareAjax()) {
            $this->dataPostReplace($result);
        }

        return $result;
    }
}

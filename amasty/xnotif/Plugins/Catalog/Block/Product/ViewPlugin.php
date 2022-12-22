<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Out of Stock Notification for Magento 2
*/

namespace Amasty\Xnotif\Plugins\Catalog\Block\Product;

use Amasty\Xnotif\Helper\Config;

/**
 * Show swatches when all simple products is out of stock
 */
class ViewPlugin
{
    /**
     * @var Config
     */
    private $helper;

    public function __construct(Config $helper)
    {
        $this->helper = $helper;
    }

    public function afterGetChildHtml($subject, $result, $alias = '', $useCache = true)
    {
        if ($alias == 'form_bottom' && !$subject->getProduct()->isSaleable() && $this->helper->isShowOutOfStockOnly()) {
            $result = $subject->getChildChildHtml('options_container') . $result;
        }

        return $result;
    }
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Out of Stock Notification for Magento 2
*/
namespace Amasty\Xnotif\Block;

/**
 * Class Stock
 */
class Stock extends \Amasty\Xnotif\Block\AbstractBlock
{
    public function _construct()
    {
        $this->setAlertType("stock");
        parent::_construct();
    }
}

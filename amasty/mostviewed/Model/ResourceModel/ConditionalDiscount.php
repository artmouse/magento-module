<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\ResourceModel;

use Amasty\Mostviewed\Api\Data\ConditionalDiscountInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ConditionalDiscount extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(ConditionalDiscountInterface::MAIN_TABLE, ConditionalDiscountInterface::ID);
    }
}

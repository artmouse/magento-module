<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Model\ResourceModel\Extension;

use Amasty\GroupAssign\Model\Extension\CustomerGroup as CustomerGroupModel;
use Magento\Rule\Model\ResourceModel\AbstractResource;

class CustomerGroup extends AbstractResource
{
    public const TABLE_NAME = 'amasty_groupassign_customer_group';

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, CustomerGroupModel::ID);
    }
}

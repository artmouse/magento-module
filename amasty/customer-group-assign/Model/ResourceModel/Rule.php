<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Model\ResourceModel;

use Magento\Rule\Model\ResourceModel\AbstractResource;

class Rule extends AbstractResource
{
    public const TABLE_NAME = 'amasty_groupassign_rule_table';

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }
}

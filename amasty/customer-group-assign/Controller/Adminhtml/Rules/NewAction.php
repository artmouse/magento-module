<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Controller\Adminhtml\Rules;

use Amasty\GroupAssign\Controller\Adminhtml\AbstractRules;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends AbstractRules
{
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $result->forward('edit');
    }
}

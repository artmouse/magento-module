<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Controller\Adminhtml\Rules;

use Amasty\GroupAssign\Controller\Adminhtml\AbstractRules;
use Magento\Framework\Controller\ResultFactory;

class Index extends AbstractRules
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_GroupAssign::rules');
        $resultPage->getConfig()->getTitle()->prepend(__('Rules'));
        $resultPage->addBreadcrumb(__('Rules'), __('Rules'));

        return $resultPage;
    }
}

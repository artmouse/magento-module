<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Promo Banners Base for Magento 2
*/

namespace Amasty\PromoBanners\Controller\Adminhtml\Banners;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $pageResult = $this->resultPageFactory->create();
        $pageResult->getLayout();
        $pageResult->setActiveMenu('Amasty_PromoBanners::ampromobanners');
        $pageResult->addBreadcrumb(__('Banners'), __('Banners'));
        $pageResult->getConfig()->getTitle()->prepend(__('Banners'));

        return $pageResult;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_PromoBanners::ampromobanners');
    }
}

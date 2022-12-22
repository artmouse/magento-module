<?php

namespace Amasty\Reindex\Controller\Adminhtml\Reindex;

class Index extends \Amasty\Reindex\Controller\Adminhtml\AbstractReindex
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->run();
        return $this->resultRedirectFactory->create()->setRefererUrl();
    }
}

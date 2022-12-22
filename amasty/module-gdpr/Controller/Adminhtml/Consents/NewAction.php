<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package GDPR Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Gdpr\Controller\Adminhtml\Consents;

use Amasty\Gdpr\Controller\Adminhtml\AbstractConsents;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends AbstractConsents
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $result->forward('edit');
    }
}

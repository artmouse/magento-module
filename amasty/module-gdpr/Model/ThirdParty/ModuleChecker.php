<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package GDPR Base for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Gdpr\Model\ThirdParty;

use Magento\Framework\Module\Manager;

class ModuleChecker
{
    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function isAmastyFaqEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Amasty_Faq');
    }

    public function isAmastyGdprFaqSampleDataEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Amasty_GdprFaqSampleData');
    }
}

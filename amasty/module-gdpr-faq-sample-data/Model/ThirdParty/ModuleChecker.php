<?php
declare(strict_types=1);

namespace Amasty\GdprFaqSampleData\Model\ThirdParty;

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

    public function isAmastyGdprEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Amasty_Gdpr');
    }
}

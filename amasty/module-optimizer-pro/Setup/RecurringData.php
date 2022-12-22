<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Google Page Speed Optimizer for Magento 2
*/
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizerPro\Setup;

use Magento\Framework\Module\Manager;
use Magento\Framework\Module\Status;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class RecurringData implements InstallDataInterface
{
    public const MODULES_TO_DISABLE = [
        'Amasty_PageSpeedOptimizerLite'
    ];

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Status
     */
    private $moduleStatus;

    public function __construct(
        Manager $moduleManager,
        Status $moduleStatus
    ) {
        $this->moduleManager = $moduleManager;
        $this->moduleStatus = $moduleStatus;
    }

    /**
     * @inheritDoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        foreach (self::MODULES_TO_DISABLE as $moduleName) {
            if ($this->moduleManager->isEnabled($moduleName)) {
                $this->moduleStatus->setIsEnabled(false, [$moduleName]);
            }
        }
    }
}

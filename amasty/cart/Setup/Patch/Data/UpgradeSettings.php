<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Cart\Setup\Patch\Data;

use Amasty\Cart\Setup\Operation\UpgradeSettings as UpgradeSettingsOperation;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpgradeSettings implements DataPatchInterface
{
    /**
     * @var UpgradeSettingsOperation
     */
    private $upgradeSettings;

    public function __construct(
        UpgradeSettingsOperation $upgradeSettings
    ) {
        $this->upgradeSettings = $upgradeSettings;
    }

    public function apply(): DataPatchInterface
    {
        $this->upgradeSettings->execute();

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}

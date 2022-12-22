<?php
declare(strict_types=1);

namespace Amasty\GdprFaqSampleData\Setup\Patch\Data;

use Amasty\GdprFaqSampleData\Model\ThirdParty\ModuleChecker;
use Amasty\GdprFaqSampleData\Setup\SampleData\Installer;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallSampleData implements DataPatchInterface
{
    /**
     * @var Installer
     */
    private $sampleDataInstaller;

    /**
     * @var ModuleChecker
     */
    private $moduleChecker;

    public function __construct(
        Installer $sampleDataInstaller,
        ModuleChecker $moduleChecker
    ) {
        $this->sampleDataInstaller = $sampleDataInstaller;
        $this->moduleChecker = $moduleChecker;
    }

    public function apply()
    {
        if ($this->moduleChecker->isAmastyGdprEnabled()
            && $this->moduleChecker->isAmastyFaqEnabled()
        ) {
            $this->sampleDataInstaller->install();
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}

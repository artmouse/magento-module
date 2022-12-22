<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Mega Menu Core Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\MegaMenuLite\ViewModel\Header;

use Amasty\MegaMenuLite\Model\ConfigProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Logo implements ArgumentInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function isHamburgerEnabled(): bool
    {
        return $this->configProvider->isHamburgerEnabled();
    }
}

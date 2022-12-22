<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Lazy Load for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\LazyLoad\Model\OptionSource;

use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Data\OptionSourceInterface;

class OptimizationSettings implements OptionSourceInterface
{
    public const SIMPLE = 0;
    public const ADVANCED = 1;

    use ToOptionArrayTrait;

    public function toArray(): array
    {
        return [
            self::SIMPLE => __('Simple'),
            self::ADVANCED => __('Advanced'),
        ];
    }
}

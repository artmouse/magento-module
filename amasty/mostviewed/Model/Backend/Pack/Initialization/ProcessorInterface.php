<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Backend\Pack\Initialization;

use Amasty\Mostviewed\Api\Data\PackInterface;
use Magento\Framework\Exception\LocalizedException;

interface ProcessorInterface
{
    /**
     * @param PackInterface $pack
     * @param array $inputPackData
     * @return void
     * @throws LocalizedException
     */
    public function execute(PackInterface $pack, array $inputPackData): void;
}

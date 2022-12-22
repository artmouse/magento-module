<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Backend\Pack\Initialization;

use Amasty\Mostviewed\Api\Data\PackInterface;

class StoreProcessor implements ProcessorInterface
{
    public function execute(PackInterface $pack, array $inputPackData): void
    {
        $pack->getExtensionAttributes()->setStores($inputPackData['stores'] ?? []);
    }
}

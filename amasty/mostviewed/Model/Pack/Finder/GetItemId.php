<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Finder;

class GetItemId
{
    /**
     * @var int
     */
    private $lastId = -100;

    public function execute(): int
    {
        return ++$this->lastId;
    }
}

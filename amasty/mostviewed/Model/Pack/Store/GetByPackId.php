<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Store;

use Amasty\Mostviewed\Model\ResourceModel\Pack\Store\LoadByPackId;

class GetByPackId
{
    /**
     * @var LoadByPackId
     */
    private $loadByPackId;

    public function __construct(LoadByPackId $loadByPackId)
    {
        $this->loadByPackId = $loadByPackId;
    }

    public function execute(int $packId): array
    {
        return $this->loadByPackId->execute($packId);
    }
}

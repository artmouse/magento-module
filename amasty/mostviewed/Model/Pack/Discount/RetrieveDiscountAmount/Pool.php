<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Discount\RetrieveDiscountAmount;

class Pool
{
    /**
     * @var RetrieveStrategyInterface[]
     */
    private $retrievers;

    public function __construct(array $retrievers = [])
    {
        $this->retrievers = $retrievers;
    }

    public function getRetriever(int $discountType): RetrieveStrategyInterface
    {
        return $this->retrievers[$discountType] ?? $this->retrievers['default'];
    }
}

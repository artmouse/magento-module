<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\ResourceModel\Pack\Analytic\Sales\OrderFilters;

use Amasty\Mostviewed\Model\ResourceModel\Pack\Sales\GetAggregatedByOrderTable;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;

class OrderIncludePackFilter implements OrderFilterInterface
{
    public function execute(Collection $collection, string $value): void
    {
        $condition = $value ? 'IS NOT NULL' : 'IS NULL';
        $collection->getSelect()->where(sprintf(
            '%s.%s %s',
            GetAggregatedByOrderTable::VIEW_NAME,
            GetAggregatedByOrderTable::ORDER_COLUMN,
            $condition
        ));
    }
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

declare(strict_types=1);

namespace Amasty\GroupAssign\Model;

use Magento\Framework\Data\Collection;

class BatchLoader
{
    public const BATCH_SIZE = 200;

    public function load(Collection $collection, $batchSize = self::BATCH_SIZE): \Generator
    {
        $currentPage = 1;
        $collection->setPageSize($batchSize);
        $collection->setCurPage($currentPage);
        $totalPagesCount = $collection->getLastPageNumber();

        while ($currentPage <= $totalPagesCount) {
            $collection->clear();
            $collection->setCurPage($currentPage);

            foreach ($collection->getItems() as $item) {
                yield $item;
            }

            $currentPage++;
        }
    }
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\CollectionProcessor\Category\Join;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Setup\Operation\CreateCategoryStoreTable;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class StoreId implements CustomJoinInterface
{
    public function apply(AbstractDb $collection)
    {
        $collection->getSelect()->joinLeft(
            [CreateCategoryStoreTable::TABLE_NAME => $collection->getTable(CreateCategoryStoreTable::TABLE_NAME)],
            'main_table.' . CategoryInterface::CATEGORY_ID . ' = '
            . CreateCategoryStoreTable::TABLE_NAME . '.' . CategoryInterface::CATEGORY_ID,
            []
        );
        $collection->distinct(true);
    }
}

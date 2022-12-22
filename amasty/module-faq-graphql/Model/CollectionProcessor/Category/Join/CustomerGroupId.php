<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\CollectionProcessor\Category\Join;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Setup\Operation\CreateFaqCategoryCustomerGroupTable;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class CustomerGroupId implements CustomJoinInterface
{
    public function apply(AbstractDb $collection)
    {
        $collection->getSelect()->joinLeft(
            [
                CreateFaqCategoryCustomerGroupTable::TABLE_NAME =>
                    $collection->getTable(CreateFaqCategoryCustomerGroupTable::TABLE_NAME)
            ],
            'main_table.' . CategoryInterface::CATEGORY_ID . ' = '
            . CreateFaqCategoryCustomerGroupTable::TABLE_NAME . '.' . CategoryInterface::CATEGORY_ID,
            []
        );
        $collection->distinct(true);
    }
}

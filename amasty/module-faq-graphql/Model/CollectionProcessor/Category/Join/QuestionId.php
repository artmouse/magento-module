<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\CollectionProcessor\Category\Join;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Setup\Operation\CreateQuestionCategoryTable;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class QuestionId implements CustomJoinInterface
{
    public function apply(AbstractDb $collection)
    {
        $collection->getSelect()->joinLeft(
            [CreateQuestionCategoryTable::TABLE_NAME => $collection->getTable(CreateQuestionCategoryTable::TABLE_NAME)],
            'main_table.' . CategoryInterface::CATEGORY_ID . ' = '
            . CreateQuestionCategoryTable::TABLE_NAME . '.' . CategoryInterface::CATEGORY_ID,
            []
        );
        $collection->distinct(true);
    }
}

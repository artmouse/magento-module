<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\CollectionProcessor\Question\Join;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Setup\Operation\CreateQuestionCategoryTable;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class CategoryId implements CustomJoinInterface
{
    public function apply(AbstractDb $collection)
    {
        $collection->getSelect()->joinLeft(
            [CreateQuestionCategoryTable::TABLE_NAME => $collection->getTable(CreateQuestionCategoryTable::TABLE_NAME)],
            'main_table.' . QuestionInterface::QUESTION_ID . ' = '
            . CreateQuestionCategoryTable::TABLE_NAME . '.' . QuestionInterface::QUESTION_ID,
            []
        );
        $collection->distinct(true);
    }
}

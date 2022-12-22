<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\CollectionProcessor\Question\Join;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Api\Data\TagInterface;
use Amasty\Faq\Setup\Operation\CreateQuestionTagTable;
use Amasty\Faq\Setup\Operation\CreateTagTable;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class Tag implements CustomJoinInterface
{
    public function apply(AbstractDb $collection)
    {
        $collection->getSelect()->joinLeft(
            [
                CreateQuestionTagTable::TABLE_NAME => $collection->getTable(CreateQuestionTagTable::TABLE_NAME)
            ],
            'main_table.' . QuestionInterface::QUESTION_ID . ' = '
            . CreateQuestionTagTable::TABLE_NAME . '.' . QuestionInterface::QUESTION_ID,
            []
        )->joinLeft(
            [
                CreateTagTable::TABLE_NAME => $collection->getTable(CreateTagTable::TABLE_NAME)
            ],
            CreateQuestionTagTable::TABLE_NAME . '.' . TagInterface::TAG_ID . ' = '
            . CreateTagTable::TABLE_NAME . '.' . TagInterface::TAG_ID,
            []
        );
        $collection->distinct(true);
    }
}

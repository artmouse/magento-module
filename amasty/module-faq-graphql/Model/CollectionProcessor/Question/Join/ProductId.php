<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\CollectionProcessor\Question\Join;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Setup\Operation\CreateQuestionProductTable;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class ProductId implements CustomJoinInterface
{
    public function apply(AbstractDb $collection)
    {
        $collection->getSelect()->joinLeft(
            [CreateQuestionProductTable::TABLE_NAME => $collection->getTable(CreateQuestionProductTable::TABLE_NAME)],
            'main_table.' . QuestionInterface::QUESTION_ID . ' = '
            . CreateQuestionProductTable::TABLE_NAME . '.' . QuestionInterface::QUESTION_ID,
            []
        );
        $collection->distinct(true);
    }
}

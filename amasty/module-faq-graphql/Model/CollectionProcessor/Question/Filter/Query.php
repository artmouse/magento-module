<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\CollectionProcessor\Question\Filter;

use Amasty\Faq\Api\Data\QuestionInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class Query implements CustomFilterInterface
{
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $collection->getSelect()->where(
            new \Zend_Db_Expr('main_table.' . QuestionInterface::TITLE . ' REGEXP "' . $filter->getValue() . '"')
        );

        return true;
    }
}

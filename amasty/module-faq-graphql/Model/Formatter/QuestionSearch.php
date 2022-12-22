<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\Formatter;

use Amasty\Faq\Api\Data\QuestionSearchResultsInterface;

class QuestionSearch
{
    /**
     * @var Question
     */
    private $questionFormatter;

    public function __construct(Question $questionFormatter)
    {
        $this->questionFormatter = $questionFormatter;
    }

    public function format(QuestionSearchResultsInterface $searchResults): array
    {
        $pageSize = $searchResults->getSearchCriteria()->getPageSize();
        $questions = [];

        foreach ($searchResults->getItems() as $question) {
            $questions[] = $this->questionFormatter->format($question);
        }

        return [
            'items' => $questions,
            'page_info' => [
                'page_size' => $pageSize,
                'current_page' => $searchResults->getSearchCriteria()->getCurrentPage(),
                'total_pages' => $pageSize ? ((int)ceil($searchResults->getTotalCount() / $pageSize)) : 0
            ],
            'total_count' => $searchResults->getTotalCount()
        ];
    }
}

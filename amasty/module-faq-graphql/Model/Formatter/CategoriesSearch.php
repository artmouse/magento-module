<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\Formatter;

use Amasty\Faq\Api\Data\CategorySearchResultsInterface;

class CategoriesSearch
{
    /**
     * @var Category
     */
    private $categoryFormatter;

    public function __construct(
        Category $categoryFormatter
    ) {
        $this->categoryFormatter = $categoryFormatter;
    }

    public function format(CategorySearchResultsInterface $searchResults)
    {
        $pageSize = $searchResults->getSearchCriteria()->getPageSize();
        $categories = [];

        foreach ($searchResults->getItems() as $question) {
            $categories[] = $this->categoryFormatter->format($question);
        }

        return [
            'items' => $categories,
            'page_info' => [
                'page_size' => $pageSize,
                'current_page' => $searchResults->getSearchCriteria()->getCurrentPage(),
                'total_pages' => $pageSize ? ((int)ceil($searchResults->getTotalCount() / $pageSize)) : 0
            ],
            'total_count' => $searchResults->getTotalCount()
        ];
    }
}

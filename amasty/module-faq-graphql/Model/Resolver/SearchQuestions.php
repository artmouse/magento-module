<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\Resolver;

use Amasty\Faq\Api\QuestionRepositoryInterface;
use Amasty\FaqGraphQl\Model\Formatter\QuestionSearch;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class SearchQuestions implements ResolverInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var QuestionRepositoryInterface
     */
    private $questionRepository;

    /**
     * @var QuestionSearch
     */
    private $questionSearchFormatter;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var array
     */
    private $filterFieldsMap;

    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        QuestionRepositoryInterface $questionRepository,
        QuestionSearch $questionSearchFormatter,
        SortOrderBuilder $sortOrderBuilder,
        array $filterFieldsMap = []
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilderFactory->create();
        $this->questionRepository = $questionRepository;
        $this->questionSearchFormatter = $questionSearchFormatter;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->filterFieldsMap = $filterFieldsMap;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateInput($args);

        if (isset($args['search'])) {
            $this->applySearch($args['search']);
        }
        if (isset($args['filter'])) {
            $this->applyFilters($args['filter']);
        }
        if (isset($args['sort'])) {
            $this->applySort($args['sort']);
        }
        $this->searchCriteriaBuilder->setCurrentPage($args['currentPage']);
        $this->searchCriteriaBuilder->setPageSize($args['pageSize']);

        return $this->questionSearchFormatter->format(
            $this->questionRepository->getList($this->searchCriteriaBuilder->create())
        );
    }

    private function validateInput(array $args): void
    {
        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
    }

    private function applySearch(string $query)
    {
        if (empty($query) || !(preg_match_all('/(\w{2,})/isu', $query, $words))) {
            return;
        }
        $words[1] = $words[1] ?? [];
        $this->searchCriteriaBuilder->addFilter('query', implode('|', $words[1]), null);
    }

    private function applyFilters(array $filters): void
    {
        foreach ($filters as $fieldName => $conditions) {
            foreach ($conditions as $conditionType => $value) {
                $fieldName = $this->filterFieldsMap[$fieldName] ?? $fieldName;
                $this->searchCriteriaBuilder->addFilter($fieldName, $value, $conditionType);
            }
        }
    }

    private function applySort(array $sort): void
    {
        foreach ($sort as $fieldName => $order) {
            $this->searchCriteriaBuilder->addSortOrder(
                $this->sortOrderBuilder->setField($fieldName)
                    ->setDirection($order)
                    ->create()
            );
        }
    }
}

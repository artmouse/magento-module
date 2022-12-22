<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\Formatter;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Model\ConfigProvider;
use Magento\Cms\Model\Template\FilterProvider;

class Question
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    public function __construct(
        ConfigProvider $configProvider,
        FilterProvider $filterProvider
    ) {
        $this->configProvider = $configProvider;
        $this->filterProvider = $filterProvider;
    }

    public function format(QuestionInterface $question): array
    {
        $questionData = $question->getData();

        $questionData['short_answer'] = $question->prepareShortAnswer(
            $this->configProvider->getLimitShortAnswer(),
            $this->configProvider->getFaqPageShortAnswerBehavior()
        );
        $questionData['answer'] = $this->filterProvider->getPageFilter()->filter($question->getAnswer());
        $questionData['category_ids'] = $this->prepareExplodedValue((string)$question->getCategories());
        $questionData['store_ids'] = $this->prepareExplodedValue((string)$question->getStores());
        $questionData['tags'] = $this->prepareExplodedValue((string)$question->getTagTitles());
        $questionData['product_ids'] = $this->prepareExplodedValue((string)$question->getProductIds());
        $questionData['product_category_ids'] = $this->prepareExplodedValue((string)$question->getProductCategoryIds());
        $questionData['customer_groups'] = $this->prepareExplodedValue((string)$question->getCustomerGroups());

        return $questionData;
    }

    private function prepareExplodedValue(string $value): ?array
    {
        return array_filter(explode(',', $value)) ?: null;
    }
}

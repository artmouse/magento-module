<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Faq Graph Ql for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\FaqGraphQl\Model\Resolver\Product;

use Amasty\Faq\Model\ResourceModel\Question\CollectionFactory;
use Amasty\FaqGraphQl\Model\Formatter\Question;
use Magento\Catalog\Model\Product;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GetQuestions implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    private $questionsCollectionFactory;

    /**
     * @var Question
     */
    private $questionFormatter;

    public function __construct(
        CollectionFactory $questionsCollectionFactory,
        Question $questionFormatter
    ) {
        $this->questionsCollectionFactory = $questionsCollectionFactory;
        $this->questionFormatter = $questionFormatter;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new GraphQlInputException(__('"model" value should be specified'));
        }

        /** @var Product $product */
        $product = $value['model'];
        $collection = $this->questionsCollectionFactory->create()->addProductFilter($product->getId());

        return array_map(function ($question) {
            return $this->questionFormatter->format($question);
        }, $collection->getItems());
    }
}

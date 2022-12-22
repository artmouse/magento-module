<?php
declare(strict_types=1);

namespace Amasty\Paction\Model\Command;

use Amasty\Paction\Model\EntityResolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category;
use Magento\Framework\App\ResourceConnection;

class Replacecategory extends Addcategory
{
    public const TYPE = 'replacecategory';

    public function __construct(
        ResourceConnection $resource,
        Category\CollectionFactory $categoryCollectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        EntityResolver $entityResolver
    ) {
        parent::__construct($resource, $categoryCollectionFactory, $categoryRepository, $entityResolver);

        $this->type = self::TYPE;
        $this->info = [
            'confirm_title' => __('Replace Category')->render(),
            'confirm_message' => __('Are you sure you want to replace category?')->render(),
            'type' => $this->type,
            'label' => __('Replace Category')->render(),
            'fieldLabel' => __('Category IDs')->render(),
            'placeholder' => __('id1,id2,id3')->render()
        ];
    }
}

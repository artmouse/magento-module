<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Auto Assign for Magento 2
*/

namespace Amasty\GroupAssign\Ui\DataProvider\Listing;

class RulesDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Amasty\GroupAssign\Model\ResourceModel\Rule\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Amasty\GroupAssign\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
    }

    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
        }

        return $this->collection;
    }
}

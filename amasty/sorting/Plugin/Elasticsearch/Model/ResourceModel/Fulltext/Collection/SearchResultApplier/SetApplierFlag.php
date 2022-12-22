<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Improved Sorting for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Sorting\Plugin\Elasticsearch\Model\ResourceModel\Fulltext\Collection\SearchResultApplier;

use Amasty\Sorting\Model\Elasticsearch\ApplierFlag;
use Magento\Elasticsearch\Model\ResourceModel\Fulltext\Collection\SearchResultApplier;

class SetApplierFlag
{
    /**
     * @var ApplierFlag
     */
    private $applierFlag;

    public function __construct(ApplierFlag $applierFlag)
    {
        $this->applierFlag = $applierFlag;
    }

    public function aroundApply(SearchResultApplier $subject, callable $proceed): void
    {
        $this->applierFlag->enable();
        $proceed();
        $this->applierFlag->disable();
    }
}

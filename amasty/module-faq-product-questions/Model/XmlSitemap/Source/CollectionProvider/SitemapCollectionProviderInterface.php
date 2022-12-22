<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package FAQ and Product Questions for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Faq\Model\XmlSitemap\Source\CollectionProvider;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

interface SitemapCollectionProviderInterface
{
    /**
     * @return AbstractCollection
     */
    public function getCollection(int $storeId): AbstractCollection;
}

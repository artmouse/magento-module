<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package XML Google® Sitemap for Magento 2
*/

declare(strict_types=1);

namespace Amasty\XmlSitemap\Model\ResourceModel\Sitemap\Grid;

use Amasty\XmlSitemap\Api\SitemapInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    protected function _construct(): void
    {
        $this->addFilterToMap(
            SitemapInterface::SITEMAP_ID,
            sprintf('main_table.%s', SitemapInterface::SITEMAP_ID)
        );
    }
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Promo Banners Base for Magento 2
*/
declare(strict_types=1);

namespace Amasty\PromoBanners\ViewModel;

use Amasty\PromoBanners\Model\Banner\Data;
use Amasty\PromoBanners\Model\Rule;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Framework\Registry;

class BannersDataProvider implements ArgumentInterface
{
    public const SEARCH_PAGE_URL = '/catalogsearch/result/';

    /**
     * @var Data
     */
    private $dataSource;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * Needs to use Registry, because on different pages, request ID param applies to Product or Category
     * @var Registry
     */
    private $registry;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        Data $dataSource,
        Escaper $escaper,
        Registry $registry,
        RequestInterface $request
    ) {
        $this->dataSource = $dataSource;
        $this->escaper = $escaper;
        $this->registry = $registry;
        $this->request = $request;
    }

    public function getBanners(): array
    {
        $product = $categoryId = $searchQuery = null;

        /** @var \Magento\Catalog\Model\Product $product */
        if ($this->registry->registry('current_product')) {
            $product = $this->registry->registry('current_product');
        }

        if ($this->registry->registry('current_category')) {
            $category = $this->registry->registry('current_category');
            $categoryId = (int)$category->getId();
        }

        if (str_contains($this->request->getPathInfo(), self::SEARCH_PAGE_URL)) {
            $searchQuery = $this->escaper->escapeUrl($this->request->getParam(QueryFactory::QUERY_VAR_NAME));
        }

        $bannersDetails = $this->dataSource->getBanners($product, $categoryId, $searchQuery);
        $bannersDetails['injectorSectionId'] = Rule::POS_AMONG_PRODUCTS;

        return $bannersDetails;
    }
}

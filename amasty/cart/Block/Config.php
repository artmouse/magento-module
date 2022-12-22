<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/
/**
 * Copyright © 2016 Amasty. All rights reserved.
 */
namespace Amasty\Cart\Block;

use Amasty\Cart\Helper\Data;
use Amasty\Cart\Plugin\DataPost\Replacer;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Config extends Template
{
    public const PRODUCT_PAGE = 'product';
    public const CATEGORY_PAGE = 'category';

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var array
     */
    private $ajaxElements = [
        'a',
        'button',
        'span'
    ];

    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->setTemplate('Amasty_Cart::config.phtml');
        $this->includeAssets();
    }

    /**
     * @return Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return string
     */
    public function getDataPostSelector()
    {
        $attr = '[' . Replacer::DATA_POST_AJAX . ']';
        $selectors = array_map(
            function ($ajaxElement) use ($attr) {
                return $ajaxElement . $attr;
            },
            $this->ajaxElements
        );

        return $this->helper->encode($selectors);
    }

    public function includeAssets()
    {
        if ($this->helper->isSliderWork()) {
            // Insert slick slider css after carousel css
            $this->pageConfig->getAssetCollection()->insert(
                Data::SLICK_STYLES,
                $this->_assetRepo->createAsset(Data::SLICK_STYLES),
                Data::CAROUSEL_STYLES
            );
        }
    }

    /**
     * @return bool
     */
    public function isAjaxAdd()
    {
        return ($this->getPageType() == self::PRODUCT_PAGE && $this->getHelper()->isUsedOnProductPage())
            || $this->getPageType() == self::CATEGORY_PAGE;
    }
}

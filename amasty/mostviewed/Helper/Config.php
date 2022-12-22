<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Automatic Related Products for Magento 2
*/

declare(strict_types = 1);

namespace Amasty\Mostviewed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\DesignInterface;

class Config extends AbstractHelper
{
    public const MODULE_PATH = 'ammostviewed/';

    public const DEFAULT_GATHERED_PERIOD = 30;

    public const BUNDLE_PAGE_PATH = 'ammostviewed/bundle_packs/cms_page';

    public const IGNORE_ANCHOR_CATEGORIES = 'general/ignore_anchor_categories';

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    /**
     * @var \Amasty\Mostviewed\Model\Rule\Condition\CombineFactory
     */
    private $combineFactory;

    /**
     * @var \Amasty\Mostviewed\Model\Rule\Condition\SameAsCombineFactory
     */
    private $sameAsCombineFactory;

    /**
     * @var \Amasty\Mostviewed\Model\Indexer\RuleProcessor
     */
    private $ruleProcessor;

    /**
     * inject objects for prevent fatal on cloud
     */
    public function __construct(
        \Amasty\Mostviewed\Model\Rule\Condition\CombineFactory $combineFactory,
        \Amasty\Mostviewed\Model\Rule\Condition\SameAsCombineFactory $sameAsCombineFactory,
        \Amasty\Mostviewed\Model\Indexer\RuleProcessor $ruleProcessor,
        \Magento\Framework\Filter\FilterManager $filterManager,
        Context $context
    ) {
        parent::__construct($context);
        $this->filterManager = $filterManager;
        $this->combineFactory = $combineFactory;
        $this->sameAsCombineFactory = $sameAsCombineFactory;
        $this->ruleProcessor = $ruleProcessor;
    }

    /**
     * @param $path
     * @param int $storeId
     *
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::MODULE_PATH . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return bool|null
     */
    public function isIgnoreAnchorCategories(): bool
    {
        return (bool)$this->getModuleConfig(self::IGNORE_ANCHOR_CATEGORIES);
    }

    /**
     * @return int
     */
    public function getGatheredPeriod()
    {
        $period = $this->getModuleConfig('general/period');
        if (!$period) {
            $period = self::DEFAULT_GATHERED_PERIOD;
        }

        return $period;
    }

    public function getOrderStatus(): array
    {
        $value = $this->getModuleConfig('general/order_status');

        return $value ? explode(',', $value) : [];
    }

    /**
     * @return bool
     */
    public function isBlockInCartEnabled()
    {
        return (bool)$this->getModuleConfig('bundle_packs/display_cart_block');
    }

    /**
     * @return int
     */
    public function isTopMenuEnabled()
    {
        return $this->getModuleConfig('bundle_packs/top_menu_enabled');
    }

    /**
     * @return string
     */
    public function getBlockPosition()
    {
        return $this->getModuleConfig('bundle_packs/position');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->filterManager->stripTags(
            $this->getModuleConfig('bundle_packs/tab_title'),
            [
                'allowableTags' => null,
                'escape' => true
            ]
        );
    }

    /**
     * @param null|int $storeId
     *
     * @return int
     */
    public function getThemeForStore($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            DesignInterface::XML_PATH_THEME_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}

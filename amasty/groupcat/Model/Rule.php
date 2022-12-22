<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Customer Group Catalog for Magento 2
*/

namespace Amasty\Groupcat\Model;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\DataObject;

/**
 * Note: Rule can be for All Store View (sore_ids = array(0))
 *
 * @method \Amasty\Groupcat\Model\Rule setStoreIds(string $value)
 * @method \Amasty\Groupcat\Model\Rule setCustomerGroupIds(string $value)
 * @method \Amasty\Groupcat\Model\Rule setCategoryIds(string $value)
 * @method \Amasty\Groupcat\Model\ResourceModel\Rule _getResource()
 * @method \Amasty\Groupcat\Model\ResourceModel\Rule getResource()
 * @method Rule\Condition\ActionConditions\Combine getActions()
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Rule extends \Magento\Rule\Model\AbstractModel implements \Amasty\Groupcat\Api\Data\RuleInterface
{
    public const CACHE_TAG = 'amsty_groupcat_rule';

    /**
     * @var \Amasty\Groupcat\Model\Rule\Condition\CombineFactory
     */
    protected $combineFactory;

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $productIds;

    /**
     * @var array
     */
    protected $customerIds;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Action\CollectionFactory
     */
    private $actionCombineFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Amasty\Groupcat\Model\Rule\Condition\RuleConditions\CombineFactory $combineFactory,
        \Amasty\Groupcat\Model\Rule\Condition\ActionConditions\CombineFactory $actionCombineFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
        $this->combineFactory = $combineFactory;
        $this->actionCombineFactory = $actionCombineFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Model Init
     *
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Groupcat\Model\ResourceModel\Rule::class);
        $this->setIdFieldName('rule_id');
    }

    /**
     * Getter for rule conditions collection. Product Conditions
     *
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * Getter for rule actions collection. Customer Condition
     *
     * @return \Amasty\Groupcat\Model\Rule\Condition\ActionConditions\Combine
     */
    public function getActionsInstance()
    {
        return $this->actionCombineFactory->create();
    }

    /**
     * Get rule associated website Ids
     * Note: Rule can be for All Store View (sore_ids = array(0))
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        if (!$this->hasWebsiteIds()) {
            $stores = $this->getStoreIds();
            $websiteIds = [];
            foreach ($stores as $storeId) {
                $websiteIds[] = $this->storeManager->getStore($storeId)->getWebsiteId();
            }
            $this->setData('website_ids', array_unique($websiteIds));
        }
        return $this->_getData('website_ids');
    }

    /**
     * Get rule associated store Ids
     * Note: Rule can be for All Store View (sore_ids = array(0 => '0'))
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (!$this->hasStoreIds()) {
            $storeIds = $this->_getResource()->getStoreIds($this->getId());
            $this->setData('store_ids', (array)$storeIds);
        }
        return $this->_getData('store_ids');
    }

    /**
     * Get rule associated category Ids
     *
     * @return array
     */
    public function getCategoryIds()
    {
        if (!$this->hasCategoryIds()) {
            $categoryIds = $this->_getResource()->getCategoryIds($this->getId());
            $this->setData('category_ids', (array)$categoryIds);
        }
        return $this->_getData('category_ids');
    }

    /**
     * Get rule associated category Ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_getResource()->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

    /**
     * Prepare data before saving
     *
     * @return $this
     */
    public function beforeSave()
    {
        /**
         * Prepare category Ids if applicable and if they were set as string in comma separated format.
         * Backwards compatibility.
         */
        if ($this->hasCategoryIds()) {
            $categoryIds = $this->getCategoryIds();
            if (is_string($categoryIds) && !empty($categoryIds)) {
                $this->setCategoryIds(explode(',', $categoryIds));
            }
        }

        if (!(int)$this->getBlockIdView()) {
            $this->setBlockIdView(null);
        }

        if (!(int)$this->getBlockIdList()) {
            $this->setBlockIdList(null);
        }

        if (!(int)$this->getForbiddenPageId()) {
            $this->setForbiddenPageId(null);
        }

        if ($this->getHideProduct() && !$this->getAllowDirectLinks()) {
            $this->setBlockIdView(null);
            $this->setBlockIdList(null);
        }

        parent::beforeSave();
        return $this;
    }

    /**
     * Get array of product ids which are matched by rule
     * Initializing by Indexer. Stored in Index
     *
     * @return array
     */
    public function getMatchingProductIds(): array
    {
        if ($this->productIds === null) {
            $this->productIds = $params = [];

            if ($this->getStoreIds() != [0]) {
                $params['website_id'] = $this->getWebsiteIds();
            }

            foreach ($this->getStoreIds() as $storeId) {
                $params['store_id'] = $storeId;
                $productIds = $this->getConditions()->getSatisfiedIds($params);
                foreach ($productIds as $productId) {
                    $this->productIds[$productId][$storeId] = true;
                }
            }
        }

        return $this->productIds;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return bool
     */
    public function validateCustomer($customer)
    {
        $params = [];
        if ($this->getCustomerGroupEnabled()) {
            if (!in_array((int)$customer->getGroupId(), $this->getCustomerGroupIds())) {
                return false;
            }

            $params['group_id'] = $this->getCustomerGroupIds();
        }

        return $this->getActions()->isSatisfiedBy($customer, $params);
    }

    /**
     * Get array of customer ids which are matched by rule
     * Initializing by Indexer. Stored in Index
     *
     * @return array
     */
    public function getMatchingCustomerIds(): array
    {
        if ($this->customerIds === null) {
            $params = [];
            if ($this->getCustomerGroupEnabled()) {
                if ($this->getCustomerGroupIds() == [GroupInterface::NOT_LOGGED_IN_ID]) {
                    return $this->customerIds = [];
                }

                $params['group_id'] = $this->getCustomerGroupIds();
            }

            $this->customerIds = $this->getActions()->getSatisfiedIds($params);
        }

        return $this->customerIds;
    }

    /**
     * Validate if rule can run
     *
     * @param DataObject|Product $product
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate(DataObject $product): bool
    {
        $params = [];
        if ($this->getStoreIds() != [0]) {
            $params['website_id'] = $this->getWebsiteIds();
        }

        return $this->getConditions()->isSatisfiedBy($product, $params);
    }

    /**
     * Validate rule data
     *
     * @param DataObject|Rule $dataObject
     * @return bool|string[] - return true if validation passed successfully. Array with errors description otherwise
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validateData(DataObject $dataObject)
    {
        $result = [];
        if ($dataObject->getDateRangeEnabled()) {
            $fromDate = $toDate = null;

            if ($dataObject->hasFromDate() && $dataObject->hasToDate()) {
                $fromDate = $dataObject->getFromDate();
                $toDate   = $dataObject->getToDate();
            }

            if ($fromDate && $toDate) {
                $fromDate = new \DateTime($fromDate);
                $toDate   = new \DateTime($toDate);

                if ($fromDate > $toDate) {
                    $result[] = __('End Date must follow Start Date.');
                }
            }
        }

        if ($dataObject->hasStoreIds()) {
            $storeIds = $dataObject->getStoreIds();
            if (empty($storeIds)) {
                $result[] = __('Please specify a store.');
            }
        }
        if ($dataObject->getCustomerGroupEnabled() && $dataObject->hasCustomerGroupIds()) {
            $customerGroupIds = $dataObject->getCustomerGroupIds();
            if (empty($customerGroupIds)) {
                $result[] = __('Please specify Customer Groups.');
            }
        }

        return count($result) ? $result : true;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    // phpcs:ignore Generic.Metrics.NestingLevel.TooHigh
    protected function _convertFlatToRecursive(array $data)
    {
        $arr = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'conditions':
                case 'actions':
                    $arr = $this->actionsBuild($key, $value, $arr);
                    // no setData for conditions
                    break;
                case 'from_date':
                case 'to_date':
                    /** Convert dates into \DateTime|null */
                    if ($value && $data['date_range_enabled']) {
                        $value = new \DateTime($value);
                    } else {
                        $value = null;
                    }

                    $this->setData($key, $value);
                    break;
                case 'store_ids':
                    /**
                     * Avoid selected "All Store Views" and some store in same time
                     * in this case, delete option "All Store Views" from selection.
                     * "All Store Views" can be selected only as single option
                     */
                    if (count($value) > 1 && in_array('0', $value)) {
                        foreach ($value as $storeIndex => $storeId) {
                            if ($storeId == '0') {
                                unset($value[$storeIndex]);
                            }
                        }
                    }

                    $this->setData($key, $value);
                    break;
                default:
                    $this->setData($key, $value);
                    break;
            }
        }

        return $arr;
    }

    /**
     * @param string $formName
     *
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    public function getActionsFieldSetId(string $formName = ''): string
    {
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }

    /**
     * @deprecated @since 1.2.0 field name changed to is_active
     * @return int
     */
    public function getEnabled()
    {
        return $this->getIsActive();
    }

    /**
     * @deprecated @since 1.2.0 field name changed to is_active
     * @param $isActive
     *
     * @return $this
     */
    public function setEnabled($isActive)
    {
        return $this->setIsActive($isActive);
    }

    /**#@+
     * Standard Getter and Setters start
     */
    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * {@inheritdoc}
     */
    public function getForbiddenAction()
    {
        return $this->getData(self::FORBIDDEN_ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setForbiddenAction($action)
    {
        return $this->setData(self::FORBIDDEN_ACTION, $action);
    }

    /**
     * {@inheritdoc}
     */
    public function getForbiddenPageId()
    {
        return $this->getData(self::FORBIDDEN_PAGE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setForbiddenPageId($cmsPageId)
    {
        return $this->setData(self::FORBIDDEN_PAGE_ID, $cmsPageId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowDirectLinks()
    {
        return $this->getData(self::ALLOW_DIRECT_LINKS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAllowDirectLinks($flag)
    {
        return $this->setData(self::ALLOW_DIRECT_LINKS, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getHideProduct()
    {
        return $this->getData(self::HIDE_PRODUCT);
    }

    /**
     * {@inheritdoc}
     */
    public function setHideProduct($flag)
    {
        return $this->setData(self::HIDE_PRODUCT, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getHideCategory()
    {
        return $this->getData(self::HIDE_CATEGORY);
    }

    /**
     * {@inheritdoc}
     */
    public function setHideCategory($flag)
    {
        return $this->setData(self::HIDE_CATEGORY, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getHideCart()
    {
        return $this->getData(self::HIDE_CART);
    }

    /**
     * {@inheritdoc}
     */
    public function setHideCart($option)
    {
        return $this->setData(self::HIDE_CART, $option);
    }

    /**
     * {@inheritdoc}
     */
    public function getHideWishlist()
    {
        return $this->getData(self::HIDE_WISHLIST);
    }

    /**
     * {@inheritdoc}
     */
    public function setHideWishlist($option)
    {
        return $this->setData(self::HIDE_WISHLIST, $option);
    }

    /**
     * {@inheritdoc}
     */
    public function getHideCompare()
    {
        return $this->getData(self::HIDE_COMPARE);
    }

    /**
     * {@inheritdoc}
     */
    public function setHideCompare($option)
    {
        return $this->setData(self::HIDE_COMPARE, $option);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceAction()
    {
        return $this->getData(self::PRICE_ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceAction($option)
    {
        return $this->setData(self::PRICE_ACTION, $option);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockIdView()
    {
        return $this->getData(self::BLOCK_ID_VIEW);
    }

    /**
     * {@inheritdoc}
     */
    public function setBlockIdView($cmsBlockId)
    {
        return $this->setData(self::BLOCK_ID_VIEW, $cmsBlockId);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockIdList()
    {
        return $this->getData(self::BLOCK_ID_LIST);
    }

    /**
     * {@inheritdoc}
     */
    public function setBlockIdList($cmsBlockId)
    {
        return $this->setData(self::BLOCK_ID_LIST, $cmsBlockId);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromDate()
    {
        return $this->getData(self::FROM_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFromDate($date)
    {
        return $this->setData(self::FROM_DATE, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function getToDate()
    {
        return $this->getData(self::TO_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setToDate($date)
    {
        return $this->setData(self::TO_DATE, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function getDateRangeEnabled()
    {
        return $this->getData(self::DATE_RANGE_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setDateRangeEnabled($flag)
    {
        return $this->setData(self::DATE_RANGE_ENABLED, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroupEnabled()
    {
        return $this->getData(self::CUSTOMER_GROUP_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroupEnabled($flag)
    {
        return $this->setData(self::CUSTOMER_GROUP_ENABLED, $flag);
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }
    /**#@-
     * Standard Getter and Setters finish
     */

    protected function actionsBuild($key, $value, $arr)
    {
        if (is_array($value)) {
            foreach ($value as $id => $conditions) {
                $path = explode('--', $id);
                $node = &$arr;
                for ($i = 0, $l = count($path); $i < $l; $i++) {
                    if (!isset($node[$key][$path[$i]])) {
                        $node[$key][$path[$i]] = [];
                    }
                    $node = &$node[$key][$path[$i]];
                }
                foreach ($conditions as $k => $v) {
                    $node[$k] = $v;
                }
            }
        }
        return $arr;
    }
}

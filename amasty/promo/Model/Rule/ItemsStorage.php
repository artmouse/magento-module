<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Free Gift Base for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Promo\Model\Rule;

use Amasty\Promo\Helper\Item;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;

class ItemsStorage
{
    /**
     * @var Item
     */
    private $promoItemHelper;

    /**
     * @var array
     */
    private $storage = [];

    /**
     * @var array
     */
    private $validItemIds = [];

    public function __construct(
        Item $promoItemHelper
    ) {
        $this->promoItemHelper = $promoItemHelper;
    }

    /**
     * @param AbstractItem $item
     * @param int $ruleId
     * @return array
     */
    public function getItems(AbstractItem $item, int $ruleId): array
    {
        if (!isset($this->storage[$ruleId])) {
            $this->storage[$ruleId] = $item->getQuote()->getAllVisibleItems();
        }

        return (array)$this->storage[$ruleId];
    }

    /**
     * @param Rule $rule
     * @param AbstractItem[] $items
     * @return array
     */
    public function getValidItemIdsForRule(Rule $rule, array $items): array
    {
        $ruleId = $rule->getRuleId();

        if (!isset($this->validItemIds[$ruleId])) {
            $validItemIds = [];

            foreach ($items as $item) {
                if (!$item || $this->promoItemHelper->isPromoItem($item) || $item->getProduct()->getParentProductId()) {
                    continue;
                }

                if (!$rule->getActions()->validate($item)) {

                    // if condition not valid for Parent, but valid for child then collect qty of child
                    foreach ((array)$item->getChildren() as $child) {
                        if ($rule->getActions()->validate($child)) {
                            $validItemIds[] = (int)$item->getId();
                        }
                    }
                } else {
                    $validItemIds[] = (int)$item->getId();
                }
            }

            $this->validItemIds = [$ruleId => $validItemIds];
        }

        return $this->validItemIds[$ruleId];
    }

    /**
     * @return void
     */
    public function reset(): void
    {
        $this->storage = [];
        $this->validItemIds = [];
    }
}

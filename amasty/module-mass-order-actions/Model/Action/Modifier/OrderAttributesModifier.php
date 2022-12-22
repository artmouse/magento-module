<?php
declare(strict_types=1);

namespace Amasty\Oaction\Model\Action\Modifier;

use Amasty\Oaction\Model\OrderAttributesChecker;

class OrderAttributesModifier implements ActionModifierInterface
{
    public const ACTION_ORDER_ATTRIBUTES = 'amasty_oaction_orderattr';

    /**
     * @var OrderAttributesChecker
     */
    private $orderAttributesChecker;

    public function __construct(OrderAttributesChecker $orderAttributesChecker)
    {
        $this->orderAttributesChecker = $orderAttributesChecker;
    }

    public function modify(array &$item): void
    {
        if (!$this->orderAttributesChecker->isModuleExist(false)) {
            unset($item[self::ACTION_ORDER_ATTRIBUTES]);
        }
    }
}

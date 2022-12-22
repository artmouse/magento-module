<?php
declare(strict_types=1);

namespace Amasty\Oaction\Model\Action;

use Amasty\Oaction\Model\Action\Modifier\ActionModifierInterface;

class ActionModifierProvider
{
    /**
     * @var ActionModifierInterface[]
     */
    private $modifiers = [];

    public function __construct(
        array $modifiers = []
    ) {
        $this->initializeModifiers($modifiers);
    }

    /**
     * @param string $actionName
     * @return ActionModifierInterface
     */
    public function get(string $actionName): ?ActionModifierInterface
    {
        return $this->modifiers[$actionName] ?? null;
    }

    private function initializeModifiers(array $modifiers): void
    {
        foreach ($modifiers as $actionName => $modifier) {
            if (!$modifier instanceof ActionModifierInterface) {
                throw new \LogicException(
                    sprintf('Modifier must implement %s', ActionModifierInterface::class)
                );
            }

            $this->modifiers[$actionName] = $modifier;
        }
    }
}

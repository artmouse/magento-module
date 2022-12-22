<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Lazy Load for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\LazyLoad\Model\LazyScript;

use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Data\OptionSourceInterface;

class LazyScriptProvider implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $lazyScripts = [];

    public function __construct(array $lazyScripts = [])
    {
        /** @var LazyScriptInterface $lazyScript */
        foreach ($lazyScripts as $lazyScript) {
            $this->lazyScripts[$lazyScript->getType()] = $lazyScript;
        }
    }

    public function get($key): ?LazyScriptInterface
    {
        return $this->lazyScripts[$key] ?? null;
    }

    use ToOptionArrayTrait;

    public function toArray(): array
    {
        $result = [];
        /** @var LazyScriptInterface $lazyScript */
        foreach ($this->lazyScripts as $lazyScript) {
            $result[$lazyScript->getType()] = $lazyScript->getName();
        }

        return $result;
    }
}

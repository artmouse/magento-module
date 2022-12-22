<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Product Labels for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Label\Model;

use Amasty\Label\Api\Data\LabelInterface;

class LabelRegistry
{
    public const CURRENT_LABEL = 'amasty_current_label';
    public const PERSISTED_DATA = 'persisted_data';

    /**
     * @var object[]
     */
    private $dataCollection = [];

    public function registry($key)
    {
        return $this->dataCollection[$key] ?? null;
    }

    public function register($key, $value, $graceful = false): void
    {
        if (isset($this->dataCollection[$key])) {
            if ($graceful) {
                return;
            }

            throw new \RuntimeException(__('Registry key "%1" already exists', $key)->render());
        }

        $this->dataCollection[$key] = $value;
    }

    public function getCurrentLabel(): ?LabelInterface
    {
        $label = $this->registry(self::CURRENT_LABEL);

        return $label instanceof LabelInterface ? $label : null;
    }

    public function setCurrentLabel(LabelInterface $label): void
    {
        $this->register(self::CURRENT_LABEL, $label, true);
    }
}

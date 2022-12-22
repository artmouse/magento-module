<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Google Page Speed Optimizer Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ServerPushAssetTypes implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $assetTypes;

    public function __construct(array $assetTypes = [])
    {
        $this->assetTypes = $assetTypes;
    }

    public function toOptionArray()
    {
        $optionArray = [];

        foreach ($this->assetTypes as $assetType => $label) {
            $optionArray[] = ['value' => $assetType, 'label' => $label];
        }

        return $optionArray;
    }
}

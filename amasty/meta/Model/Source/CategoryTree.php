<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Meta Tags Templates for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Meta\Model\Source;

class CategoryTree implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $dataHelper;

    public function __construct(\Amasty\Meta\Helper\Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->dataHelper->getTree();
    }
}

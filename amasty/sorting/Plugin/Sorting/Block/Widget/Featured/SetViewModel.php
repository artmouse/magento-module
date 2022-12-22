<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Improved Sorting for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Sorting\Plugin\Sorting\Block\Widget\Featured;

use Amasty\Sorting\Block\Widget\Featured;
use Amasty\Sorting\ViewModel\Helpers;

class SetViewModel
{
    /**
     * @var Helpers
     */
    private $helpers;

    public function __construct(Helpers $helpers)
    {
        $this->helpers = $helpers;
    }

    /**
     * @param Featured $featuredWidget
     */
    public function beforeToHtml(Featured $featuredWidget): void
    {
        $featuredWidget->setData('helpers', $this->helpers);
    }
}

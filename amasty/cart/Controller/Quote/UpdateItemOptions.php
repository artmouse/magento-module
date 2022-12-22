<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/

namespace Amasty\Cart\Controller\Quote;

use Amasty\Cart\Controller\Cart\UpdateItemOptions as CartUpdate;
use Amasty\Cart\Model\Source\Section;

/**
 * Controller used when Request A Quote module installed
 *
 * Class UpdateItemOptions
 */
class UpdateItemOptions extends CartUpdate
{
    /**
     * @var string
     */
    protected $type = Section::QUOTE;
}

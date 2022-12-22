<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package FAQ and Product Questions for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Faq\Model;

use Amasty\Faq\Api\Data\QuestionSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Service Data Object with Questions search results.
 */
class QuestionSearchResults extends SearchResults implements QuestionSearchResultsInterface
{
}

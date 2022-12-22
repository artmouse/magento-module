<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Custom Form Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Customform\ViewModel\Customer;

use Magento\Customer\Model\SessionFactory;

class CustomerInfoFromCustomerSession implements CustomerInfoProviderInterface
{
    /**
     * @var SessionFactory
     */
    private $customerSessionFactory;

    public function __construct(
        SessionFactory $customerSessionFactory
    ) {
        $this->customerSessionFactory = $customerSessionFactory;
    }

    public function getCustomerId(): int
    {
        return (int) $this->customerSessionFactory->create()->getCustomerId();
    }

    public function getCustomerGroupId(): int
    {
        return (int) $this->customerSessionFactory->create()->getCustomerGroupId();
    }
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package GDPR Base for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Gdpr\Observer\Checkout;

class ConsentRegistry
{
    /**
     * @var array
     */
    protected $consents = [];

    public function setConsents(array $consents): void
    {
        $this->consents = $consents;
    }

    public function getConsents(): array
    {
        return $this->consents;
    }

    public function resetConsents(): void
    {
        $this->consents = [];
    }
}

<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Meta Tags Templates for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Meta\Console\Command;

class GeneratorWithRedirect extends AbstractGenerator
{
    const AMMETA_GENERATOR_WITH_REDIRECT = 'ammeta:generate:with-redirect';

    protected function configure(): void
    {
        $this->setName(self::AMMETA_GENERATOR_WITH_REDIRECT);
        $this->setDescription(__('If product pages were already indexed'
            . ' and it’s required to create permanent redirects.'));

        parent::configure();
    }

    protected function isNeedRedirect(): bool
    {
        return true;
    }
}

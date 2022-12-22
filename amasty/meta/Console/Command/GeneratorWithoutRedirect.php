<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Meta Tags Templates for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Meta\Console\Command;

class GeneratorWithoutRedirect extends AbstractGenerator
{
    const AMMETA_GENERATOR_WITHOUT_REDIRECT = 'ammeta:generate:without-redirect';

    protected function configure(): void
    {
        $this->setName(self::AMMETA_GENERATOR_WITHOUT_REDIRECT);
        $this->setDescription(__('If you don’t need to create redirects.'));

        parent::configure();
    }

    protected function isNeedRedirect(): bool
    {
        return false;
    }
}

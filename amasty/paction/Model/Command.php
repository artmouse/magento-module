<?php
declare(strict_types=1);

namespace Amasty\Paction\Model;

abstract class Command
{
    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var array
     */
    protected $info = [];

    /**
     * @var array
     */
    protected $errors = [];

    abstract public function execute(array $ids, int $storeId, string $val);

    public function getCreationData(): array
    {
        return $this->info;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

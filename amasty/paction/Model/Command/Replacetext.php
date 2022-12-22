<?php
declare(strict_types=1);

namespace Amasty\Paction\Model\Command;

use Amasty\Paction\Model\Command;
use Amasty\Paction\Model\ConfigProvider;
use Amasty\Paction\Model\EntityResolver;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Replacetext extends Command
{
    public const REPLACE_MODIFICATOR = '->';
    public const REPLACE_FIELD = 'value';
    public const TYPE = 'replacetext';

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var EntityResolver
     */
    private $entityResolver;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        Config $eavConfig,
        ResourceConnection $resource,
        EntityResolver $entityResolver,
        ConfigProvider $configProvider
    ) {
        $this->eavConfig = $eavConfig;
        $this->connection = $resource->getConnection();
        $this->entityResolver = $entityResolver;
        $this->configProvider = $configProvider;
        $this->resource = $resource;

        $this->type = self::TYPE;
        $this->info = [
            'confirm_title' => __('Replace Text')->render(),
            'confirm_message' => __('Are you sure you want to replace text?')->render(),
            'type' => $this->type,
            'label' => __('Replace Text')->render(),
            'fieldLabel' => __('Replace')->render(),
            'placeholder' => __('search->replace')->render()
        ];
    }

    public function execute(array $ids, int $storeId, string $val): Phrase
    {
        $searchReplace = $this->generateReplaces($val);
        $this->searchAndReplace($searchReplace, $ids, $storeId);

        return __('Total of %1 products(s) have been successfully updated.', count($ids));
    }

    protected function generateReplaces(string $inputText): array
    {
        $modificatorPosition = stripos($inputText, self::REPLACE_MODIFICATOR);

        if ($modificatorPosition === false) {
            throw new LocalizedException(__('Replace field must contain: search->replace'));
        }

        $search = trim(substr($inputText, 0, $modificatorPosition));
        $text = trim(
            substr(
                $inputText,
                (strlen($search) + strlen(self::REPLACE_MODIFICATOR)),
                strlen($inputText)
            )
        );

        return [$search, $text];
    }

    protected function searchAndReplace(array $searchReplace, array $ids, int $storeId): void
    {
        list($search, $replace) = $searchReplace;
        $attrGroups = $this->getAttrGroups();
        $entityIdName = $this->entityResolver->getEntityLinkField(ProductInterface::class);

        foreach ($attrGroups as $backendType => $attrIds) {
            if ($backendType === AbstractAttribute::TYPE_STATIC) {
                $this->processStaticAttributes($attrIds, $ids, $search, $replace);
            } else {
                $this->processAttributes($attrIds, $ids, $search, $replace, $backendType, $storeId);
            }
        }
    }

    protected function getSetSql(string $attrName, string $search, string $replace): \Zend_Db_expr
    {
        return new \Zend_Db_expr(sprintf(
            'REPLACE(`%s`, %s, %s)',
            $attrName,
            $this->connection->quote($search),
            $this->connection->quote($replace)
        ));
    }

    protected function getAttrGroups(): array
    {
        $productAttributes = $this->configProvider->getReplaceAttributes();
        $attrGroups = [];

        foreach ($productAttributes as $item) {
            $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $item);
            $attrGroups[$attribute->getBackendType()][$attribute->getId()] = $attribute->getName();
        }

        return $attrGroups;
    }

    private function processAttributes(
        array $attrIds,
        array $ids,
        string $search,
        string $replace,
        string $backendType,
        int $storeId
    ): void {
        $entityIdName = $this->entityResolver->getEntityLinkField(ProductInterface::class);
        $table = $this->resource->getTableName('catalog_product_entity_' . $backendType);

        foreach (array_keys($attrIds) as $attrId) {
            $valuesSelect = $this->connection->select()
                ->from($table, $entityIdName)
                ->where($entityIdName . ' IN (?)', $ids)
                ->where('store_id = ?', $storeId)
                ->where('attribute_id = ?', $attrId);
            $existsValues = $this->connection->fetchCol($valuesSelect);

            if ($existsValues) {
                $this->connection->update(
                    $table,
                    [self::REPLACE_FIELD => $this->getSetSql(self::REPLACE_FIELD, $search, $replace)],
                    [
                        'store_id = ?' => $storeId,
                        'attribute_id = ?' => $attrId,
                        $entityIdName . ' IN (?)' => $existsValues
                    ]
                );
            }

            $defaultValueIds = array_diff($ids, $existsValues);

            if ($defaultValueIds) {
                $valuesSelect = $this->connection->select()
                    ->from($table, [$entityIdName, 'attribute_id', 'value'])
                    ->where($entityIdName . ' IN (?)', $defaultValueIds)
                    ->where('store_id = ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
                    ->where('attribute_id = ?', $attrId);

                if ($defaultValues = $this->connection->fetchAll($valuesSelect)) {
                    $storeValues = array_map(function ($defaultValue) use ($storeId, $search, $replace) {
                        $defaultValue['store_id'] = $storeId;
                        $defaultValue['value'] = str_replace($search, $replace, $defaultValue['value']);

                        return $defaultValue;
                    }, $defaultValues);
                    $this->connection->insertMultiple($table, $storeValues);
                }
            }
        }
    }

    private function processStaticAttributes(array $attrIds, array $ids, string $search, string $replace): void
    {
        $entityIdName = $this->entityResolver->getEntityLinkField(ProductInterface::class);
        $table = $this->resource->getTableName('catalog_product_entity');
        $set = [];

        foreach ($attrIds as $attrName) {
            $set[$attrName] = $this->getSetSql($attrName, $search, $replace);
        }

        $this->connection->update(
            $table,
            $set,
            [$entityIdName . ' IN (?)' => $ids]
        );
    }
}

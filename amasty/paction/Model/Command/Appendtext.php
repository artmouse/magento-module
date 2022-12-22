<?php
declare(strict_types=1);

namespace Amasty\Paction\Model\Command;

use Amasty\Paction\Model\Command;
use Amasty\Paction\Model\ConfigProvider;
use Amasty\Paction\Model\EntityResolver;
use Amasty\Paction\Model\Source\Append;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Repository as ProductAttributeRepository;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;

class Appendtext extends Command
{
    public const TYPE = 'appendtext';
    public const MODIFICATOR = '->';
    public const FIELD = 'value';

    /**
     * @var ProductAttributeRepository
     */
    protected $productAttributeRepository;

    /**
     * @var EntityResolver
     */
    private $entityResolver;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        ResourceConnection $resource,
        ProductAttributeRepository $productAttributeRepository,
        EntityResolver $entityResolver,
        ConfigProvider $configProvider
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->productAttributeRepository = $productAttributeRepository;
        $this->entityResolver = $entityResolver;
        $this->configProvider = $configProvider;

        $this->type = self::TYPE;
        $this->info = [
            'confirm_title' => __('Append Text')->render(),
            'confirm_message' => __('Are you sure you want to append text?')->render(),
            'type' => $this->type,
            'label' => __('Append Text')->render(),
            'fieldLabel' => __('Append')->render(),
            'placeholder' => __('attribute_code->text')->render()
        ];
    }

    public function execute(array $ids, int $storeId, string $val): Phrase
    {
        $row = $this->generateAppend($val);
        $this->appendText($row, $ids, $storeId);

        return __('Total of %1 products(s) have been successfully updated.', count($ids));
    }

    protected function generateAppend(string $inputText): array
    {
        $modificatorPosition = stripos($inputText, self::MODIFICATOR);

        if ($modificatorPosition === false) {
            throw new LocalizedException(__('Field must contain "' . self::MODIFICATOR . '"'));
        }

        $search = trim(substr($inputText, 0, $modificatorPosition));
        $text = substr(
            $inputText,
            (strlen($search) + strlen(self::MODIFICATOR)),
            strlen($inputText)
        );

        return [$search, $text];
    }

    protected function appendText(array $searchReplace, array $ids, int $storeId): void
    {
        list($attributeCode, $appendText) = $searchReplace;

        try {
            $attribute = $this->productAttributeRepository->get($attributeCode);
            $attrId = $attribute->getAttributeId();
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('There is no product attribute with code `%1`, ', $attributeCode));
        }

        $entityIdName = $this->entityResolver->getEntityLinkField(ProductInterface::class);

        if ($attribute->getBackendType() === AbstractAttribute::TYPE_STATIC) {
            $table = $this->resource->getTableName('catalog_product_entity');
            $this->connection->update(
                $table,
                $this->addSetSql($appendText, $storeId, $attributeCode),
                [$entityIdName . ' IN (?)' => $ids]
            );
        } else {
            $table = $this->resource->getTableName('catalog_product_entity_' . $attribute->getBackendType());
            $valuesSelect = $this->connection->select()
                ->from($table, $entityIdName)
                ->where($entityIdName . ' IN (?)', $ids)
                ->where('store_id = ?', $storeId)
                ->where('attribute_id = ?', $attrId);
            $existsValues = $this->connection->fetchCol($valuesSelect);

            if ($existsValues) {
                $this->connection->update(
                    $table,
                    $this->addSetSql($appendText, $storeId, $attributeCode),
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
                    $position = $this->configProvider->getAppendTextPosition($storeId);

                    $storeValues = array_map(function ($defaultValue) use ($storeId, $appendText, $position) {
                        $defaultValue['store_id'] = $storeId;

                        if ($position == Append::POSITION_BEFORE) {
                            $defaultValue['value'] =  $appendText . $defaultValue['value'];
                        } else {
                            $defaultValue['value'] =  $defaultValue['value'] . $appendText;
                        }

                        return $defaultValue;
                    }, $defaultValues);

                    $this->connection->insertMultiple($table, $storeValues);
                }
            }
        }
    }

    protected function addSetSql(string $appendText, int $storeId, string $attributeCode): array
    {
        $field = $attributeCode == 'sku' ? 'sku' : self::FIELD;
        $position = $this->configProvider->getAppendTextPosition($storeId);
        $appendText = $this->connection->quote($appendText);

        if ($position == Append::POSITION_BEFORE) {
            $firstPart = $appendText;
            $secondPart = $field;
        } else {
            $firstPart = $field;
            $secondPart = $appendText;
        }

        return [$field => new \Zend_Db_Expr(sprintf(' CONCAT(%s, %s)', $firstPart, $secondPart))];
    }
}

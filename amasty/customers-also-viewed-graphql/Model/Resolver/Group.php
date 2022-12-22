<?php

declare(strict_types=1);

namespace Amasty\MostviewedGraphQl\Model\Resolver;

use Amasty\Mostviewed\Api\Data\GroupInterface;
use Amasty\Mostviewed\Api\GroupRepositoryInterface;
use Amasty\Mostviewed\Model\OptionSource\BlockPosition;
use Amasty\Mostviewed\Model\OptionSource\RuleType;
use Amasty\MostviewedGraphQl\Model\ProductInfo;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;

class Group implements ResolverInterface
{
    /**
     * @var \Amasty\Mostviewed\Block\Widget\Related
     */
    private $related;

    /**
     * @var BlockPosition
     */
    private $blockPosition;

    /**
     * @var ProductInfo
     */
    private $productInfo;

    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Uid
     */
    private $uidEncoder;

    public function __construct(
        \Amasty\Mostviewed\Block\Widget\Related $related,
        BlockPosition $blockPosition,
        ProductInfo $productInfo,
        GroupRepositoryInterface $groupRepository,
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository,
        Uid $uidEncoder
    ) {
        $this->related = $related;
        $this->blockPosition = $blockPosition;
        $this->productInfo = $productInfo;
        $this->groupRepository = $groupRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->uidEncoder = $uidEncoder;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws \Exception
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            $data = $this->getData($args['uid'], $args['position'] ?? '');
        } catch (\Exception $e) {
            return ['error' => 'Wrong parameters.'];
        }

        return $data;
    }

    private function getData(string $id, string $position): array
    {
        $data = [];
        if ($id) {
            $id = (int) $this->uidEncoder->decode($id);
        }
        $this->related->setEntityId($id);
        if ($position) {
            $groups = [$this->groupRepository->getGroupByIdAndPosition($id, $position)];
        } else {
            $groups = $this->groupRepository->getGroupsByEntityId($id);
        }

        foreach ($groups as $key => $group) {
            $this->updateRelatedData($group, $id);
            $productItems = $this->related->getProductCollection()->getItems();

            $data['items'][$key] = [
                'block_title' => $group->getData('block_title'),
                'block_layout' => (int) $group->getData('block_layout'),
                'add_to_cart' => $group->getData('add_to_cart'),
                'position' => $group->getData('block_position'),
                'items' => $productItems ? $this->getProductData($productItems) : [],
                'model' => $group
            ];
        }

        return $data;
    }

    /**
     * @param $productItems
     * @return array
     */
    private function getProductData($productItems): array
    {
        foreach ($productItems as $productKey => $product) {
            $data[$productKey] = $this->productInfo->getProductInfo($product);
        }

        return $data ?? [];
    }

    /**
     * @param GroupInterface $group
     */
    private function updateRelatedData(GroupInterface $group, int $entityId)
    {
        $position = $group->getData('block_position');
        $this->related->setCurrentGroup($group);
        $this->related->setPosition($position);
        $this->related->setEntity($this->getEntity($entityId, $position));
        $this->related->clearProductCollection();
    }

    /**
     * @return \Magento\Catalog\Model\Product|\Magento\Catalog\Model\Category
     */
    private function getEntity(int $id, string $position)
    {
        switch ($this->blockPosition->getTypeByValue($position)['value'] ?? '') {
            case RuleType::PRODUCT:
                $entity = $this->productRepository->getById($id);
                break;
            case RuleType::CATEGORY:
                $entity = $this->categoryRepository->get($id);
                break;
        }

        return $entity ?? null;
    }
}

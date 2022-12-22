<?php
declare(strict_types=1);

namespace Amasty\Paction\Controller\Adminhtml\Massaction;

use Amasty\Paction\Model\CommandResolver;
use Amasty\Paction\Model\EntityResolver;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Catalog\Model\Indexer\Product\Price\Processor;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\MassAction\Filter;

class Index extends Product
{
    public const ADMIN_RESOURCE = 'Amasty_Paction::paction';

    /**
     * @var string[]
     */
    private $entityIdRequiredActions = [
        'addcategory',
        'removecategory',
        'replacecategory'
    ];

    /**
     * @var Processor
     */
    protected $productPriceIndexerProcessor;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CommandResolver
     */
    private $commandResolver;

    /**
     * @var EntityResolver
     */
    private $entityResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context $context,
        Product\Builder $productBuilder,
        Processor $productPriceIndexerProcessor,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CommandResolver $commandResolver,
        EntityResolver $entityResolver,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $productBuilder);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productPriceIndexerProcessor = $productPriceIndexerProcessor;
        $this->commandResolver = $commandResolver;
        $this->entityResolver = $entityResolver;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        try {
            list($actionValue, $action) = $this->prepareActionData();

            if ($command = $this->commandResolver->getCommand($action)) {
                $productIds = $this->getProductIds();
                $filters = $this->getRequest()->getParam('filters') ?? [];
                $storeId = (int)($filters['store_id'] ?? Store::DEFAULT_STORE_ID);
                $this->storeManager->setCurrentStore($storeId);
                $result = $command->execute($productIds, $storeId, $actionValue);

                if ($result instanceof ResponseInterface) {
                    return $result;
                }
                $this->messageManager->addSuccessMessage($result);
                // show non critical errors to the user
                foreach ($command->getErrors() as $err) {
                    $this->messageManager->addErrorMessage($err);
                }
                $this->productPriceIndexerProcessor->reindexList($productIds);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while updating the product(s) data.')
            );
        }

        return $this->resultRedirectFactory->create()->setRefererUrl();
    }

    protected function prepareActionData(): array
    {
        $action = $this->getRequest()->getParam('action');
        $actionValue = $this->getRequest()->getParam('amasty_paction_field')
            ??  $this->getRequest()->getParam('amasty_file_field', '');

        if (strpos($action, 'amasty_') === 0) {
            $action = str_replace("amasty_", "", $action);
        } else {
            throw new LocalizedException(__('Something was wrong. Please try again.'));
        }

        return [trim($actionValue), $action];
    }

    protected function getProductIds($isEntityAction = false): array
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        if ($idsFromRequest = $this->getRequest()->getParam('selected', 0)) {
            $collection->addFieldToFilter('entity_id', ['IN' => $idsFromRequest]);
        }

        $entityLinkField = $isEntityAction
            ? 'entity_id'
            : $this->entityResolver->getEntityLinkField(ProductInterface::class);

        return $collection->getColumnValues($entityLinkField);
    }

    private function isEntityIdRequiredAction(string $action): bool
    {
        return in_array($action, $this->entityIdRequiredActions);
    }
}

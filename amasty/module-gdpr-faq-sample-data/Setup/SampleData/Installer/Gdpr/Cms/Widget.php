<?php
declare(strict_types=1);

namespace Amasty\GdprFaqSampleData\Setup\SampleData\Installer\Gdpr\Cms;

use Amasty\GdprFaqSampleData\Setup\SampleData\Installer\Faq\Gdpr\Category\DataProcessor;
use Amasty\GdprFaqSampleData\Setup\SampleData\Installer\Reader;
use Magento\Framework\View\Design\Theme\LabelFactory;
use Magento\Widget\Model\Widget\InstanceFactory;
use Magento\Widget\Model\ResourceModel\Widget\Instance as ResourceWidgetInstance;
use Magento\Widget\Model\ResourceModel\Widget\Instance\Collection;
use Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\SampleData\InstallerInterface;

class Widget implements InstallerInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var InstanceFactory
     */
    private $widgetFactory;

    /**
     * @var ResourceWidgetInstance
     */
    private $resourceWidgetInstance;

    /**
     * @var CollectionFactory
     */
    private $widgetCollectionFactory;

    /**
     * @var DataProcessor
     */
    private $categoryDataProcessor;

    /**
     * @var LabelFactory
     */
    private $themeLabelFactory;

    /**
     * @var string
     */
    private $fileName = 'Amasty_GdprFaqSampleData::fixtures/gdpr/cms/widget.csv';

    public function __construct(
        Reader $reader,
        InstanceFactory $widgetFactory,
        ResourceWidgetInstance $resourceWidgetInstance,
        CollectionFactory $widgetCollectionFactory,
        LabelFactory $themeLabelFactory,
        DataProcessor $categoryDataProcessor
    ) {
        $this->reader = $reader;
        $this->widgetFactory = $widgetFactory;
        $this->resourceWidgetInstance = $resourceWidgetInstance;
        $this->widgetCollectionFactory = $widgetCollectionFactory;
        $this->themeLabelFactory = $themeLabelFactory;
        $this->categoryDataProcessor = $categoryDataProcessor;
    }

    public function install(): void
    {
        $rows = $this->reader->readFile($this->fileName);
        foreach ($rows as $row) {
            if (!$this->isExists($row['title'])) {
                $this->create($row);
            }
        }
    }

    /**
     * @param string $identifier
     * @return bool
     * @throws LocalizedException
     */
    private function isExists(string $identifier): bool
    {
        /** @var Collection $widgetCollection */
        $widgetCollection = $this->widgetCollectionFactory->create();
        $result = $widgetCollection
            ->addFieldToFilter('title', $identifier)
            ->setCurPage(1)
            ->setPageSize(1)
            ->getItems();

        return count($result) > 0;
    }

    /**
     * @param array $row
     * @throws \Exception
     * @throws LocalizedException
     */
    private function create(array $row): void
    {
        $themeId = $this->getThemeIdByLabel($row['theme_id']);
        if (!$themeId) {
            return;
        }
        $object = $this->widgetFactory->create();
        $categoryUrl = $row['widget_parameters']['faq_categories'];
        $row['widget_parameters']['faq_categories'] = $this->categoryDataProcessor->getCategoryIdByUrlKey($categoryUrl);
        $row['store_ids'] = explode(',', $row['store_ids']);
        $row['theme_id'] = $themeId;
        $row['page_groups'] = json_decode($row['page_groups'], true);
        $object->addData($row);

        $this->resourceWidgetInstance->save($object);
    }

    private function getThemeIdByLabel(string $label): ?int
    {
        $themeLabel = $this->themeLabelFactory->create();
        $options = $themeLabel->getLabelsCollection();

        $result = null;
        foreach ($options as $option) {
            if ($option['label'] == $label) {
                $result = (int)$option['value'];
                break;
            }
        }

        return $result;
    }
}

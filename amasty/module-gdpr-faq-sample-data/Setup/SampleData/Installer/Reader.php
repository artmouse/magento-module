<?php
declare(strict_types=1);

namespace Amasty\GdprFaqSampleData\Setup\SampleData\Installer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Framework\Setup\SampleData\FixtureManager;

class Reader
{
    /**
     * @var FixtureManager
     */
    private $fixtureManager;

    /**
     * @var Csv
     */
    private $csvReader;

    public function __construct(
        SampleDataContext $sampleDataContext
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
    }

    /**
     * @param string $fileName
     * @return array
     * @throws LocalizedException
     */
    public function readFile(string $fileName): array
    {
        $fileName = $this->fixtureManager->getFixture($fileName);
        if (!file_exists($fileName)) {
            throw new LocalizedException(__('File %1 not found.', $fileName));
        }

        return $this->convertRowData($this->csvReader->getData($fileName));
    }

    private function convertRowData(array $rows): array
    {
        $header = array_shift($rows);
        foreach ($rows as &$row) {
            $data = [];
            foreach ($row as $key => $value) {
                $headerKeys = explode('.', $header[$key]);
                if (count($headerKeys) == 2) {
                    $data[$headerKeys[0]][$headerKeys[1]] = $value;
                } elseif (count($headerKeys) == 1) {
                    $data[$header[$key]] = $value;
                }
            }
            $row = $data;
        }

        return $rows;
    }
}

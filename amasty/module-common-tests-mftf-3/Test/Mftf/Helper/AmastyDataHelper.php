<?php

declare(strict_types=1);

namespace Amasty\CommonTestsMFTF3\Test\Mftf\Helper;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Helper\Helper;

class AmastyDataHelper extends Helper
{
    const CREATED_NAME = 'name';
    const CREATED_STEP_FROM = 'step_from';
    const CREATED_COUNT = 'count';
    const CREATED_STEP_TO = 'step_to';
    const REQ_NUMB = 'numb';
    const REQ_SEP = 'sep';


    /** Create Entity
     *
     * @param string $entitiesName              Name of xml entities to create.
     * @param string $entitiesCountSeparators   Quantity to create for each type of entity.
     * @param string $createStep                StepKey of the createData action.
     * @param int $createFrom                   The sequence number (suffix) of the stepKey from which needs begin to create entity.
     * @param string $requiredKeys              StepKeys of other createData actions that are required.
     *
     * @return void
     */
    public function amastyCreateEntities(
        string $entitiesName,
        string $entitiesCountSeparators,
        string $createStep,
        int $createFrom,
        string $requiredKeys
    ): void
    {
        $handler = PersistedObjectHandler::getInstance();
        $entitiesName = $this->processingStringToArray($entitiesName);
        $entitiesCountSeparators = $this->processingStringToArray($entitiesCountSeparators);
        if ($requiredKeys == 'empty') {
            $requiredKeys = [];
        } else {
            $requiredKeys = $this->processingStringToArray($requiredKeys);
        }

        try {
            $allProdCount = 1;
            $finalCreatedProducts = [];
            foreach ($entitiesName as $entityNumb => $entityName) {
                $currCount = 1;
                $finalCreatedProducts[$entityNumb][self::CREATED_NAME] = $entityName;
                $finalCreatedProducts[$entityNumb][self::CREATED_STEP_FROM] = $createFrom;
                $prodCount = $this->validateValueFromArray($entitiesCountSeparators, $entityNumb, 1);
                while ($currCount <= $prodCount) {
                    $handler->createEntity($createStep . $createFrom, 'test', $entityName, $requiredKeys);
                    $currCount++;
                    $createFrom++;
                    $allProdCount++;
                }

                $finalCreatedProducts[$entityNumb][self::CREATED_COUNT] = $currCount - 1;
                $finalCreatedProducts[$entityNumb][self::CREATED_STEP_TO] = $createFrom - 1;
            }

            $this->generateSucMessCreateEntity($requiredKeys,$createStep, ($allProdCount-1), $finalCreatedProducts);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }


    /** Create Entity with Different Distribution of Required Keys
     *
     * @param string $entityName        Name of xml entity to create.
     * @param int $entityCount          Quantity to create of entity.
     * @param string $createStep        StepKey of the createData action.
     * @param int $createFrom           The sequence number (suffix) of the stepKey from which needs begin to create entity.
     * @param string $requiredKeys      StepKeys of other createData actions that are required.
     * @param string $requiredKeysFrom  The sequence number (suffix) of the StepKeys of other createData required actions from which needs begin to create entity.
     * @param string $separators        The number of products after which it is necessary to change the sequence number of the corresponding required key.
     *
     * @return void
     */
    public function amastyCreateRequiredSeparateEntities(
        string $entityName,
        int $entityCount,
        string $createStep,
        int $createFrom,
        string $requiredKeys,
        string $requiredKeysFrom,
        string $separators
    ): void
    {
        $handler = PersistedObjectHandler::getInstance();
        $requiredKeys = $this->processingStringToArray($requiredKeys);
        $requiredKeysFrom = $this->processingStringToArray($requiredKeysFrom);
        $separators = $this->processingStringToArray($separators);
        $reqSepArray = [];
        $currCount = 1;
        $lastSucRequire = [];
        $allRequired = [];
        $currNumber = $createFrom;
        $firstStep = $createStep . $createFrom;

        // create new array with key, start number suffix and separator for each available require
        foreach ($requiredKeys as $reqNumb => $reqName) {
            $reqSepArray[$reqName][self::REQ_NUMB] = $this->validateValueFromArray($requiredKeysFrom, $reqNumb, 1);
            $reqSepArray[$reqName][self::REQ_SEP] = $this->validateValueFromArray($separators, $reqNumb, 1);
        }

        try {
            while ($currCount <= $entityCount) {
                $finalReq = [];
                // getting sequence numbers available requiredKeys to create entity and update them numbers if needs
                foreach ($reqSepArray as $reqName => $reqVal) {
                    $resultUpdate = $this->requiredUpdater($currCount, $reqVal[self::REQ_SEP]);
                    $reqSepArray[$reqName][self::REQ_NUMB] = $reqVal[self::REQ_NUMB] + $resultUpdate;
                    $finalReq[] = $reqName . $reqSepArray[$reqName][self::REQ_NUMB];
                }

                $handler->createEntity($createStep . $currNumber, 'test', $entityName, $finalReq);

                // update array with valid required steps and fill array with count the same required steps
                $lastSucRequire = $finalReq;
                $reqAsString = implode(", ", $finalReq);
                if (array_key_exists($reqAsString, $allRequired)){
                    $allRequired[$reqAsString] += 1;
                } else {
                    $allRequired[$reqAsString] = 1;
                }

                $currCount++;
                $currNumber++;
            }

            $lastStep = $createStep . ($currNumber - 1);
            $this->generateSucMessReqEntity($firstStep, $lastStep, $entityName, ($currCount-1), $allRequired);
            echo PHP_EOL;
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "could not be found")) {
                if ($currCount == 1) {
                    $this->fail(PHP_EOL . "\t\t" . $e->getMessage() . ". Your required key was not created." . PHP_EOL);
                } else {
                    // getting the last valid requiredKeys to create and create the remaining entities
                    $failNumbStep = $currNumber;
                    while ($currCount <= $entityCount) {
                        try {
                            $step = $createStep . $currNumber;
                            $handler->createEntity($step, 'test', $entityName, $lastSucRequire);
                        } catch (TestReferenceException $e) {
                            $this->fail($e->getMessage());
                        }

                        $currCount++;
                        $currNumber++;
                    }

                    $lastStep = $createStep . ($currNumber - 1);
                    $this->generateSucMessReqEntity($firstStep, $lastStep, $entityName, ($currCount-1), $allRequired);
                    $this->generateSucMessReqExceptEntity($e, $currNumber, $failNumbStep, $createStep, $lastSucRequire);
                }
            } else {
                $this->fail($e->getMessage());
            }
        }
    }


    /** Delete Entities
     *
     * @param string $createDataKey   StepKey of the createData action.
     * @param int $deleteCount        Quantity to delete of entity.
     * @param int $deleteFrom         The sequence number (suffix) of the stepKey from which needs begin to delete entity.
     *
     * @return void
     */
    public function amastyDeleteEntities(
        string $createDataKey,
        int $deleteCount,
        int $deleteFrom
    ): void
    {
        $handler = PersistedObjectHandler::getInstance();
        $currCount = 1;
        $currNumber = $deleteFrom;
        $createFrom = $createDataKey . $deleteFrom;

        try {
            while ($currCount <= $deleteCount) {
                $deleteKey = $createDataKey . $currNumber;
                $handler->deleteEntity($deleteKey, 'test');
                $currCount++;
                $currNumber++;
            }

            $this->generateSucMessDeleteEntity($createFrom, $createDataKey, ($currNumber-1), ($currCount-1));
            echo PHP_EOL;
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), "could not be found")) {
                if ($currCount == 1) {
                    $this->fail($e->getMessage());
                } else {
                    $this->generateSucMessDeleteEntity($createFrom, $createDataKey, ($currNumber-1), ($currCount-1));
                    echo "\t" . $e->getMessage() . '.' . PHP_EOL . PHP_EOL;
                }
            } else {
                $this->fail($e->getMessage());
            }
        }
    }


    /** Delete multiple createdDataKeys entities
     *
     * @param string $createDataKeys        StepKeys of the createData action.
     * @param string $deleteCountEach       Quantity to delete of each entity.
     * @param string $deleteCountEachMax    Maximum quantity to delete of each entity if not specified deleteCount.
     * @param string $deleteFromEach        The sequence number (suffix) of the stepKey from which needs begin to delete of each entity.
     *
     * @return void
     */
    public function amastyDeleteKeysEntitiesArray (
        string $createDataKeys,
        string $deleteCountEach,
        string $deleteCountEachMax,
        string $deleteFromEach
    ): void
    {
        $createDataKeys = $this->processingStringToArray($createDataKeys);
        $deleteCountEach = $this->processingStringToArray($deleteCountEach);
        $deleteFromEach = $this->processingStringToArray($deleteFromEach);
        foreach ($createDataKeys as $entityNumb => $createStepName){
            $deleteCount = $this->validateValueFromArray($deleteCountEach, $entityNumb, (int)$deleteCountEachMax);
            $deleteFrom = $this->validateValueFromArray($deleteFromEach, $entityNumb, 1);
            $this->amastyDeleteEntities($createStepName, $deleteCount, $deleteFrom);
        }
    }


    /** Converting a string to an array and processing values
     *
     * @param string $value
     *
     * @return array
     */
    private function processingStringToArray (string $value): array
    {
        return array_map('trim', explode(',', $value));
    }


    /** Return value from array if exist and return $ifNotExistValue if not exist
     *
     * @param array $array
     * @param $key
     * @param int $ifNotExistValue
     *
     * @return int
     */
    private function validateValueFromArray (array $array, $key, int $ifNotExistValue): int
    {
        if (!empty($array[$key]) && $array[$key] != 0){
            $validValue = (int)$array[$key];
        } else {
            $validValue = $ifNotExistValue;
        }

        return $validValue;
    }


    /** Function for update sequence number requiredKey
     *
     * @param int $currCount
     * @param int $separator
     *
     * @return int
     */
    private function requiredUpdater (int $currCount, int $separator): int
    {
        $updater = (($currCount - 1) / $separator) - intdiv(($currCount - 1), $separator);
        if ($updater == 0 && $currCount != 1) {
            return 1;
        } else {
            return 0;
        }
    }


    /** Function for generate success message about create entities
     *
     * @param array $requiredKeys
     * @param string $createStep
     * @param int $allProdCount
     * @param array $finalCreatedProducts
     *
     * @return void
     */
    public function generateSucMessCreateEntity (
        array $requiredKeys,
        string $createStep,
        int $allProdCount,
        array $finalCreatedProducts
    ): void
    {
        if (count($requiredKeys)){
            echo PHP_EOL . "\t" . 'SUCCESSFULLY created ' . $allProdCount
                . ' entity(-ies) with required key(-s) "' . implode(', ', $requiredKeys) . '": ';
        } else {
            echo PHP_EOL . "\t" . 'SUCCESSFULLY created ' . $allProdCount . ' entity(-ies): ';
        }

        foreach ($finalCreatedProducts as $entityNumb => $finalVal) {
            if (count($finalCreatedProducts) > 1) {
                echo PHP_EOL . "\t\t" . 'Type ' .  ($entityNumb + 1) . '. ';
            }

            echo $finalVal[self::CREATED_NAME] . ' - ' . $finalVal[self::CREATED_COUNT] . ' entity(-ies), stepKey ';

            if ($finalVal[self::CREATED_STEP_TO] - $finalVal[self::CREATED_STEP_FROM]) {
                echo 'from ' . $createStep . $finalVal[self::CREATED_STEP_FROM] . ' to ' . $createStep
                    . $finalVal[self::CREATED_STEP_TO] . '.';
            } else {
                echo  $createStep . $finalVal[self::CREATED_STEP_FROM] . '.';
            }
        }
        echo PHP_EOL . PHP_EOL;
    }


    /** Function for generate success message about create entities with separate requiredKey
     *
     * @param string $firstCreateStep
     * @param string $lastCreateStep
     * @param string $entityName
     * @param int $allCount
     * @param array $allRequired
     *
     * @return void
     */
    public function generateSucMessReqEntity (
        string $firstCreateStep,
        string $lastCreateStep,
        string $entityName,
        int $allCount,
        array $allRequired
    ): void
    {
        echo PHP_EOL . "\t" . 'SUCCESSFULLY created ' . $allCount . ' entity(-ies) (' . $entityName
            . ') with stepKey(-s) from ' . $firstCreateStep . ' to ' . $lastCreateStep . ':' . PHP_EOL;
        foreach ($allRequired as $reqName => $reqCount) {
            echo "\t\t" . "- " . $reqCount . ' entity(-ies) with required key(-s) "' . $reqName . '".' . PHP_EOL;
        }
    }


    /** Function for generate success message about create entities with separate requiredKey with exception
     *
     * @param \Exception $e
     * @param int $currNumber
     * @param int $failNumbStep
     * @param string $createStep
     * @param array $lastSucRequire
     *
     * @return void
     */
    public function generateSucMessReqExceptEntity (
        \Exception $e,
        int $currNumber,
        int $failNumbStep,
        string $createStep,
        array $lastSucRequire
    )
    {
        $nameFailStep = $createStep . $failNumbStep;
        echo "\t" . 'WARNING: ' . $e->getMessage() . '.' . PHP_EOL;
        echo "\t\t" . '- ' . ($currNumber - $failNumbStep)
            . ' entity(-ies) (last) has been created with required key(-s) "'
            . implode(', ', $lastSucRequire) . '" and stepKey(-s) from '
            . $nameFailStep . ' to ' . $createStep . ($currNumber - 1) . PHP_EOL . PHP_EOL;
    }


    /** Function for generate success message about delete entities
     *
     * @param string $createStepFrom
     * @param string $createDataKey
     * @param int $lastNumbDeleted
     * @param int $allDeleteCount
     *
     * @return void
     */
    public function generateSucMessDeleteEntity (
        string $createStepFrom,
        string $createDataKey,
        int $lastNumbDeleted,
        int $allDeleteCount
    )
    {
        echo PHP_EOL . "\t" . 'Created data from ' . $createStepFrom . ' to ' . $createDataKey . $lastNumbDeleted
            . ' has been deleted. ' . 'SUCCESSFULLY deleted ' . $allDeleteCount . ' entity(-ies).' . PHP_EOL;
    }
}

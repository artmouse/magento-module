<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AMP for Magento 2
*/

namespace Amasty\Amp\Helper;

class Deploy extends \Amasty\Base\Helper\Deploy
{
    /**
     * @param $filePath
     * @param $fromPath
     * @param $toPath
     *
     * @return mixed
     */
    protected function getNewFilePath($filePath, $fromPath, $toPath)
    {
        $newFileName = str_replace($fromPath, $toPath, $filePath);
        if ($this->rootRead->isExist($newFileName) && $this->rootRead->isFile($filePath)) {
            $this->rootWrite->delete($newFileName);
        }

        return $newFileName;
    }
}

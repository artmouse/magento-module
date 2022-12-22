<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Page Speed Tools for Magento 2 (System)
*/

namespace Amasty\PageSpeedTools\Model\Image;

use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class OutputImage extends DataObject
{
    public const PATH = 'path';
    public const PROCESS = 'process';
    public const MOBILE_PATH = 'mobile_path';
    public const TABLET_PATH = 'tablet_path';
    public const WEBP_PATH = 'webp_path';
    public const WEBP_MOBILE_PATH = 'webp_mobile_path';
    public const WEBP_TABLET_PATH = 'webp_tablet_path';

    /**
     * @var string
     */
    private $storeMediaUrl;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var File
     */
    private $file;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        File $file,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->mediaDirectory = $filesystem->getDirectoryread(DirectoryList::MEDIA);
        $this->storeMediaUrl = $storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        parent::__construct($data);
        $this->file = $file;
    }

    public function initialize(string $path): OutputImage
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        // compatibility with src without domain.
        // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
        if (!parse_url($path, PHP_URL_HOST)) {
            $path = $baseUrl . trim($path, '/');
        }
        $filePath = trim(str_replace($this->storeMediaUrl, '', $path), '/');

        if (strpos($path, $this->storeMediaUrl) === false
            || !$this->mediaDirectory->isExist($filePath)
        ) {
            $this->setProcess(false);

            return $this;
        }

        $this->setProcess(true);
        $this->setData(self::PATH, $path);
        $this->setImageData();

        return $this;
    }

    public function getPath()
    {
        return $this->_getData(self::PATH);
    }

    public function setProcess($process)
    {
        return $this->setData(self::PROCESS, (bool)$process);
    }

    public function process()
    {
        return (bool)$this->_getData(self::PROCESS);
    }

    public function setWebpPath($webpPath)
    {
        return $this->setData(self::WEBP_PATH, $webpPath);
    }

    public function getWebpPath()
    {
        return $this->_getData(self::WEBP_PATH);
    }

    public function setMobilePath($mobilePath)
    {
        return $this->setData(self::MOBILE_PATH, $mobilePath);
    }

    public function getMobilePath()
    {
        return $this->_getData(self::MOBILE_PATH);
    }

    public function setTabletPath($tabletPath)
    {
        return $this->setData(self::TABLET_PATH, $tabletPath);
    }

    public function getTabletPath()
    {
        return $this->_getData(self::TABLET_PATH);
    }

    public function setWebpMobilePath($webpMobilePath)
    {
        return $this->setData(self::WEBP_MOBILE_PATH, $webpMobilePath);
    }

    public function getWebpMobilePath()
    {
        return $this->_getData(self::WEBP_MOBILE_PATH);
    }

    public function setWebpTabletPath($webpTabletPath)
    {
        return $this->setData(self::WEBP_TABLET_PATH, $webpTabletPath);
    }

    public function getWebpTabletPath()
    {
        return $this->_getData(self::WEBP_TABLET_PATH);
    }

    public function setImageData()
    {
        $pathInfo = $this->file->getPathInfo($this->getPath());

        if (!isset($pathInfo['extension'])) {
            return null;
        }

        $baseName = $pathInfo['basename'];
        $extension = $pathInfo['extension'];
        $path = trim(str_replace($this->storeMediaUrl, '', $this->getPath()), '/');

        $webpName = str_replace('.' . $extension, '_' . $extension. '.webp', $baseName);
        $webpPath = str_replace($baseName, $webpName, Resolutions::WEBP_DIR . $path);
        if ($this->mediaDirectory->isExist($webpPath)) {
            $this->setWebpPath(str_replace($path, $webpPath, $this->getPath()));
        } else {
            $this->setWebpPath(false);
        }

        foreach (Resolutions::RESOLUTIONS as $data) {
            foreach (['webp_' => $webpName, '' => $baseName] as $prefix => $fName) {
                $curPath = $data['dir'] . $path;
                if ($this->mediaDirectory->isExist(str_replace($baseName, $fName, $curPath))) {
                    $this->setData(
                        $prefix . $data['path'],
                        str_replace($path, str_replace($baseName, $fName, $curPath), $this->getPath())
                    );
                } else {
                    $this->setData($prefix . $data['path'], false);
                }
            }
        }
    }

    public function getSourceSet()
    {
        if (!$this->getWebpPath() && !$this->getWebpMobilePath() && !$this->getWebpTabletPath()
            && !$this->getMobilePath() && !$this->getTabletPath() || !$this->process()
        ) {
            return false;
        }
        $result = '';
        foreach (Resolutions::RESOLUTIONS as $data) {
            foreach (['webp_' => 'image/webp', '' => ''] as $prefix => $type) {
                if ($this->_getData($prefix . $data['path'])) {
                    $result .= '<source srcset="' . $this->_getData($prefix . $data['path']) . '"';
                    if (!empty($data['width'])) {
                        $result .= 'media="(max-width: ' . $data['width'] . 'px)'
                            . (!empty($data['min-width']) ? 'and (min-width: ' . $data['min-width'] . 'px)' : '') . '"';
                    }
                    if (!empty($type)) {
                        $result .= 'type="' . $type . '"';
                    }
                    $result .= '>';
                }
            }
        }

        if ($this->getWebpPath()) {
            $result .= '<source srcset="' . $this->getWebpPath() . '" type="image/webp">';
        }

        if (!empty($result)) {
            $result .= '<source srcset="' . $this->getPath() . '">';
        }

        return $result;
    }

    public function getBest($type, $isWebp)
    {
        if ($isWebp) {
            switch ($type) {
                case 'desktop':
                    if ($this->getWebpPath()) {
                        return $this->getWebpPath();
                    }
                    break;
                case 'tablet':
                    if ($this->getWebpTabletPath()) {
                        return $this->getWebpTabletPath();
                    } elseif ($this->getTabletPath()) {
                        return $this->getTabletPath();
                    } elseif ($this->getWebpPath()) {
                        return $this->getWebpPath();
                    }
                    break;
                case 'mobile':
                    if ($this->getWebpMobilePath()) {
                        return $this->getWebpMobilePath();
                    } elseif ($this->getMobilePath()) {
                        return $this->getMobilePath();
                    } elseif ($this->getWebpTabletPath()) {
                        return $this->getWebpTabletPath();
                    } elseif ($this->getTabletPath()) {
                        return $this->getTabletPath();
                    } elseif ($this->getWebpPath()) {
                        return $this->getWebpPath();
                    }
                    break;
            }

            return $this->getPath();
        } else {
            switch ($type) {
                case 'tablet':
                    if ($this->getTabletPath()) {
                        return $this->getTabletPath();
                    }
                    break;
                case 'mobile':
                    if ($this->getMobilePath()) {
                        return $this->getMobilePath();
                    } elseif ($this->getTabletPath()) {
                        return $this->getTabletPath();
                    }
                    break;
            }
        }

        return $this->getPath();
    }
}

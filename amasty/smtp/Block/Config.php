<?php

namespace Amasty\Smtp\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class Config extends Template
{
    /**
     * @var \Amasty\Smtp\Model\Provider\Config
     */
    protected $providersConfig;

    public function __construct(
        Context $context,
        \Amasty\Smtp\Model\Provider\Config $providersConfig,
        array $data = []
    ) {
        $this->providersConfig = $providersConfig;
        parent::__construct($context, $data);
    }

    public function getProviders()
    {
        return $this->providersConfig->get();
    }

    protected function _toHtml()
    {
        if ($this->_request->getParam('section') == 'amsmtp') {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return [
            'store' => $this->getRequest()->getParam('store'),
            'website' => $this->getRequest()->getParam('website')
        ];
    }
}

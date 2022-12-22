<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package AJAX Shopping Cart for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Cart\Setup\Operation;

use Magento\Framework\App\ResourceConnection;

class UpgradeSettings
{
    /**
     * @var string[]
     */
    private $changedSettings = [
        '"amasty_cart/display/type_loading"' => 'amasty_cart/general/type_loading',
        '"amasty_cart/display/show_qty_product"' => 'amasty_cart/general/show_qty_product',
        '"amasty_cart/general/display_options"' => 'amasty_cart/dialog_popup/display_options',
        '"amasty_cart/general/use_product_page"' => 'amasty_cart/confirm_popup/use_on_product_page',
        '"amasty_cart/general/product_button"' => 'amasty_cart/confirm_popup/product_button',
        '"amasty_cart/display/disp_configurable_image"' => 'amasty_cart/confirm_display/configurable_image'
    ];

    /**
     * @var string[]
     */
    private $combineSettings = [
        '"amasty_cart/display/disp_product"' => 'image',
        '"amasty_cart/display/disp_count"' => 'count',
        '"amasty_cart/display/disp_checkout_button"' => 'checkout_button',
    ];

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(): void
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('core_config_data');

            $select = $this->resourceConnection->getConnection()->select()
                ->from($tableName, ['config_id', 'path'])
                ->where('path IN (' . implode(',', array_keys($this->changedSettings)) . ')');

            $settings = $connection->fetchPairs($select);

            foreach ($settings as $key => $value) {
                if (isset($this->changedSettings['"' . $value . '"'])) {
                    $connection->update(
                        $tableName,
                        ['path' => $this->changedSettings['"' . $value . '"']],
                        ['config_id = ?' => $key]
                    );
                }
            }

            $select = $this->resourceConnection->getConnection()->select()
                ->from($tableName, ['config_id', 'path'])
                ->where('path IN (' . implode(',', array_keys($this->combineSettings)) . ')');

            $settings = $connection->fetchPairs($select);
            $elements = [];
            foreach ($settings as $key => $value) {
                if (isset($this->combineSettings['"' . $value . '"'])) {
                    $elements[] = $this->combineSettings['"' . $value . '"'];
                }
            }
            if ($elements) {
                $connection->insertOnDuplicate(
                    $tableName,
                    [
                        'value' => implode(',', $elements),
                        'path'  => '"amasty_cart/confirm_display/display_elements"'
                    ]
                );
            }
        } catch (\Exception $ex) {
            null;//skip/ options is already moved
        }
    }
}

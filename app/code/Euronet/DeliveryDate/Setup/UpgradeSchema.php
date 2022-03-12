<?php
/**
 * Copyright © 2021 Euronet. All rights reserved.
 */
declare(strict_types = 1);

namespace Euronet\DeliveryDate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_item'),
                'delivery_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    'comment' =>'Delivery Date',
                    'default' => null,
                    'nullable' => true
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'delivery_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    'comment' =>'Delivery Date',
                    'default' => null,
                    'nullable' => true
                ]
            );
        }

        $setup->endSetup();
    }

}

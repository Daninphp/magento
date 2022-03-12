<?php
namespace Epay\Pricelog\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** VALIDATION TABLE START */
        $priceLogTable = $setup->getConnection()->newTable(
            $setup->getTable('epay_pricelog')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'Entity ID'
        )->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Product SKU'
        )->addColumn(
            'product_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Product Name'
        )->addColumn(
            'old_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '6,2',
            ['nullable' => false],
            'Old Product Price'
        )->addColumn(
            'new_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '6,2',
            ['nullable' => false],
            'New Product Price'
        )->addColumn(
            'admin_details',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            200,
            ['nullable' => false],
            'User/Admin that changed price'
        )->addColumn(
            'changed_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            200,
            ['nullable' => false],
            'Date and time of change'
        );

        $setup->getConnection()->createTable($priceLogTable);

        $setup->getConnection()
            ->addIndex(
                $setup->getTable('epay_pricelog'),
                $setup->getConnection()->getIndexName($setup->getTable('epay_pricelog'), ['sku', 'product_name', 'admin_details'], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT),
                ['sku', 'product_name', 'admin_details'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );


        /** VALIDATION TABLE END */

        $setup->endSetup();

    }
}


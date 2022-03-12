<?php
namespace Magexo\Pos\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        //POS table
        if (!$installer->tableExists('pos_entity')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('pos_entity')
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'ID'
                )
                ->addColumn(
                    'pos_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    20,
                    ['nullable => false'],
                    'POS id'
                )
                ->addColumn(
                    'name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    60,
                    ['nullable => false'],
                    'Name'
                )
                ->addColumn(
                    'address',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    20,
                    ['nullable => false'],
                    'address'
                )
                ->addColumn(
                    'is_available',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    10,
                    ['nullable => false'],
                    'Availability'
                )
                ->setComment('POS table');
            $installer->getConnection()->createTable($table);
        }

        $table2 = $setup->getConnection()->newTable($setup->getTable('mastering_sample_item'))
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Item Id'
        )->addColumn(
            'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Item Name'
            )->addIndex($setup->getIdxName('mastering_sample_item',['name']),
                ['name']
            )->setComment('Sample Items');

        $setup->getConnection()->createTable($table2);

        $installer->endSetup();
    }
}

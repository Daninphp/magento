<?php
namespace Syncitgroup\Sgform\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('syncit_form')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Item Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, ],
                'Name'
            )->addColumn(
                'last_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, ],
                'Last Name'
            )->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, ],
                'Email'
            )->setComment(
                'Syncit group form'
            );
            $setup->getConnection()->createTable($table);


        }

        $setup->endSetup();

    }

}


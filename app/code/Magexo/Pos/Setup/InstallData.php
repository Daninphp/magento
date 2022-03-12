<?php
namespace Magexo\Pos\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallData implements InstallDataInterface
{

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * \Magento\Framework\DB\Adapter\AdapterInterface
         */
        $conn = $setup->getConnection();

        $tableName = $setup->getTable('pos_entity');


        /**
         * Inserting data using the DB Adapter class
         */
        for($x = 1; $x <= 100; $x++){
            $data[] = [
                'pos_id' => $x,
                'name' => 'Pos Entity '. $x,
                'address' => 'Pos Address ' .$x,
                'is_available' => true
            ];
        }

        /**
         * Insert multiple rows of data into the table
         *
         * @param array $data Column-value pairs
         * @return int The number of affected rows.
         */
        $conn->insertMultiple($tableName, $data);

        $setup->getConnection()->insert($setup->getTable('mastering_sample_item'),
            [
                'name'=> 'Item 1'
            ]
        );

        $setup->getConnection()->insert($setup->getTable('mastering_sample_item'),
            [
                'name'=> 'Item 2'
            ]
        );

        $setup->endSetup();
    }
}

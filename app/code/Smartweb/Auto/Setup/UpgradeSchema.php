<?php

namespace Smartweb\Auto\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Smartweb\Auto\Helper\Data;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $eavSetup = $objectManager->get('Magento\Eav\Setup\EavSetup');

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('car_mark')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Item Id'
            )->addColumn(
                'mark_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Mark id'
            )->addColumn(
                'mark_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Mark name'
            )->addIndex(
                $setup->getIdxName('car_mark',['mark_id']),['mark_id']
            )->addIndex(
                $setup->getIdxName('car_mark',['mark_name']),['mark_name']
            )->setComment(
                'Car mark table'
            );
            $setup->getConnection()->createTable($table);

            $conn = $setup->getConnection();
            $tableName = $setup->getTable('car_mark');

            $attributeNames = Data::getInstallData();
            $data = [];
            foreach ($attributeNames as $key => $attributeName) {
                if ($key == 'Marka automobila') {
                    foreach ($attributeNames[$key] as $value) {
                        if ($value == 'Audi') {
                            $data[] = [
                                'mark_id' => DATA::AUDI_ID,
                                'mark_name' => $value
                            ];
                        } else if ($value == 'Alfa') {
                            $data[] = [
                                'mark_id' => DATA::ALFA_ID,
                                'mark_name' => $value
                            ];
                        }
                    }
                }
            }

            $conn->insertMultiple($tableName, $data);

            $table = $setup->getConnection()->newTable(
                $setup->getTable('car_model')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Item Id'
            )->addColumn(
                'mark_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Mark id'
            )->addColumn(
                'model_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Model id'
            )->addColumn(
                'model_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Model name'
            )->addIndex(
                $setup->getIdxName('car_model',['model_id']),['model_id']
            )->addIndex(
                $setup->getIdxName('car_model',['model_name']),['model_name']
            )->setComment(
                'Car model table'
            );
            $setup->getConnection()->createTable($table);

            $tableName = $setup->getTable('car_model');

            $attributeNames = Data::getInstallData();
            $data = [];

            foreach ($attributeNames as $key => $attributeName) {
                if ($key == 'Model automobila') {
                    foreach ($attributeNames[$key] as $innerKey => $value) {
                        if (strpos($innerKey, 'Audi') !== false) {
                            $data[] = [
                                'mark_id' => DATA::AUDI_ID,
                                'model_id' => $value['id'],
                                'mark_name' => $innerKey
                            ];
                        } else if (strpos($innerKey, 'Alfa') !== false) {
                            $data[] = [
                                'mark_id' => DATA::ALFA_ID,
                                'model_id' => $value['id'],
                                'mark_name' => $innerKey
                            ];
                        }
                    }
                }
            }

            $conn->insertMultiple($tableName, $data);

            $table = $setup->getConnection()->newTable(
                $setup->getTable('car_type')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Item Id'
            )->addColumn(
                'mark_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Mark id'
            )->addColumn(
                'model_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Model id'
            )->addColumn(
                'type_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Type id'
            )->addColumn(
                'type_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Type name'
            )->addIndex(
                $setup->getIdxName('car_type',['type_id']),['type_id']
            )->addIndex(
                $setup->getIdxName('car_type',['type_name']),['type_name']
            )->setComment(
                'Car type table'
            );
            $setup->getConnection()->createTable($table);

            $tableName = $setup->getTable('car_type');

            $attributeNames = Data::getInstallData();
            $data = [];

            /**
             * @var TODO CHECK IF NEEDED
             */

//            foreach ($attributeNames as $key => $attributeName) {
//                if ($key == 'Tip automobila') {
//                    foreach ($attributeNames[$key] as $innerKey => $value) {
//                        if (strpos($innerKey, 'Audi') !== false) {
//                            $data[] = [
//                                'mark_id' => DATA::AUDI_ID,
//                                'model_id' => DATA::AUDI_A6,
//                                'type_id' => $value['id'],
//                                'type_name' => $innerKey
//                            ];
//                        } else if (strpos($innerKey, 'Alfa') !== false) {
//                            $data[] = [
//                                'mark_id' => DATA::ALFA_ID,
//                                'model_id' => DATA::ALFA_147,
//                                'mark_name' => $value['id'],
//                                'type_name' => $innerKey
//                            ];
//                        }
//                    }
//                }
//            }
//
//            $conn->insertMultiple($tableName, $data);

            $table = $setup->getConnection()->newTable(
                $setup->getTable('bulb_type')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Item Id'
            )->addColumn(
                'mark_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Mark id'
            )->addColumn(
                'model_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Model id'
            )->addColumn(
                'type_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Type id'
            )->addColumn(
                'bulb_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Bulb id'
            )->addColumn(
                'bulb_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Bulb name'
            )->addIndex(
                $setup->getIdxName('bulb_type',['bulb_id']),['bulb_id']
            )->addIndex(
                $setup->getIdxName('bulb_type',['bulb_name']),['bulb_name']
            )->setComment(
                'Bulb type table'
            );
            $setup->getConnection()->createTable($table);

        }

        $setup->endSetup();
    }

}

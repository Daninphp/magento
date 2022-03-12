<?php

namespace Codiac\Search\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

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
                $setup->getTable('location_cities')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Item Id'
            )->addColumn(
                'label',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, ],
                'Label value'
            )->addColumn(
                'attribute_code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, ],
                'Attribute Code'
            )->addColumn(
                'attribute_id_value',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, ],
                'Value of attribute id'
            )->addIndex(
                $setup->getIdxName('mastering_sample_item',['label']),
                ['label']
            )->setComment(
                'Sample Items'
            );
            $setup->getConnection()->createTable($table);

            $locationsTableName = $setup->getTable('locations_full_list');
            $tableLocations = $setup->getConnection()->newTable($locationsTableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Location id'
                )->addColumn(
                    'label',
                    Table::TYPE_TEXT,
                    40,
                    ['nullable' => false, ],
                    'Postal code, area/place name, city'
                )->addColumn(
                    'attribute_code',
                    Table::TYPE_TEXT,
                    20,
                    ['nullable' => true, ],
                    'Attribute Code'
                )->addColumn(
                    'attribute_id_value',
                    Table::TYPE_TEXT,
                    20,
                    ['nullable' => true, ],
                    'Value of attribute id'
                )->addColumn(
                    'postal_code',
                    Table::TYPE_TEXT,
                    4,
                    ['nullable' => false, ],
                    'Postal code'
                )->addColumn(
                    'place_name',
                    Table::TYPE_TEXT,
                    20,
                    ['nullable' => false, ],
                    'Area/place name'
                )->addColumn(
                    'city',
                    Table::TYPE_TEXT,
                    20,
                    ['nullable' => false, ],
                    'City name'
                )->addColumn(
                    'longitude',
                    Table::TYPE_DECIMAL,
                    '6,4',
                    ['nullable' => false, ],
                    'Decimal'
                )->addColumn(
                    'latitude',
                    Table::TYPE_DECIMAL,
                    '6,4',
                    ['nullable' => false, ],
                    'Decimal'
                )->addIndex(
                    $setup->getIdxName($locationsTableName,['label']),['label']
                )->addIndex(
                    $setup->getIdxName($locationsTableName,['longitude']),['longitude']
                )->addIndex(
                    $setup->getIdxName($locationsTableName,['latitude']),['latitude']
                );

            $setup->getConnection()->createTable($tableLocations);
        } elseif (version_compare($context->getVersion(), '1.0.2', '<')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'exclusion_post_day_hour',
                [
                    'type' => 'text',
                    'label' => 'Exclusion Post - Day - Hour',
                    'input' => 'text',
                    'required' => false,
                    'user_defined' => true,
                    'sort_order' => 1000,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'group' => 'Lieferausschlusszeiten',
                    'note' => 'Format (PLZ,Tag,Stunde;) -> 1,Montag,08:00-13:00;5,Mittwoch,16:00-20:00;'
                ]
            );
        } elseif (version_compare($context->getVersion(), '1.0.3', '<')){
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_rental_order'),
                'bold_order_comment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' =>'Order Comment'
                ]
            );
        } elseif (version_compare($context->getVersion(), '1.0.4', '<')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('calendar_exclusion_days')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Item Id'
            )->addColumn(
                'exclusion_date',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, ],
                'Dates excluded for shipping'
            )->addIndex(
                $setup->getIdxName('calendar_exclusion_days',['exclusion_date']),
                ['exclusion_date']
            )->setComment(
                'Dates for excluding shipping'
            );
            $setup->getConnection()->createTable($table);
        } elseif (version_compare($context->getVersion(), '1.0.5', '<')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'hinein',
                [
                    'type' => 'text',
                    'label' => 'Hinein',
                    'input' => 'textarea',
                    'required' => false,
                    'sort_order' => 4,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'group' => 'Content',
                ]
            );
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'nicht_hinein',
                [
                    'type' => 'text',
                    'label' => 'Nicht Hinein',
                    'input' => 'textarea',
                    'required' => false,
                    'sort_order' => 4,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'group' => 'Content',
                ]
            );
        }

        $setup->endSetup();
    }

}

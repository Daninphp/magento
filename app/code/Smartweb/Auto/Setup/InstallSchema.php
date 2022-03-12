<?php
namespace Smartweb\Auto\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Smartweb\Auto\Helper\Data;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $attributeNames = Data::getInstallData();

        $attributes = [];
        foreach ($attributeNames as $key => $attributeName) {
            $attributes[] = [
                'code' => strtolower(str_replace(' ', '_', $key)),
                'label' => $key
            ];
        }

        foreach ($attributes as $attribute) {

            if($eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, $attribute['code'])) {
                $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, $attribute['code']);
            }

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                $attribute['code'],
                [
                    'type' => 'text',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'frontend' => '',
                    'label' => $attribute['label'],
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'group' => 'Models',
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'used_in_layered_navigation' => false,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );

            if($eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, $attribute['code'])) {
                $options = ['attribute_id' => null, 'values' => $attributeNames[$attribute['label']]];
                $options['attribute_id'] = $eavSetup->getAttributeId($eavSetup->getEntityTypeId('catalog_product'), $attribute['code']);
                $eavSetup->addAttributeOption($options);
            }
        }

//        try {
//            $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
//            $objectManager = $bootstrap->getObjectManager();
//            $product = $objectManager->create('Magento\Catalog\Model\Product');
//
//            for ($i = 0; $i <= 10; $i++) {
//                $sku = 'new_sku #' . $i;  // set your sku
//                $product->setSku($sku);
//                $product->setName('Simple Product Name #' . $i);
//                $product->setAttributeSetId(4);
//                $product->setStatus(1);
//                $product->setWeight(1);
//                $product->setVisibility(4);
//                $product->setWebsiteIds(array(1));
//                $product->setTaxClassId(0);
//                $product->setTypeId('simple');
//                $product->setPrice(100 + $i + rand(1, 30));
//                $product->setStockData(
//                    array(
//                        'use_config_manage_stock' => 0,
//                        'manage_stock' => 1,
//                        'is_in_stock' => 1,
//                        'qty' => 100
//                    )
//                );
//                $product->save();

//                $myCategoryId = 41; // this was my cateogryId 'Sijalice za automobile'
//                $categoryIds = array($myCategoryId); // assign your product to category using Category Id
//                $category = $objectManager->get('Magento\Catalog\Api\CategoryLinkManagementInterface');
//                $category->assignProductToCategories($sku, $categoryIds);

//            }
//        }
//        catch(\Exception $e){
//            print_r($e->getMessage());
//        }
    }

}


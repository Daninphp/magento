<?php
namespace Codiac\Search\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

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

        $letters = range('a', 'z');

        $attributes = [];

        foreach ($letters as $letter) {
            $attributes[] = [
                'code' => 'city_' . $letter,
                'label' => 'City - ' . strtoupper($letter)
            ];
        }

        if(false):  // uncomment if new installation of attributes is needed.

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
                    'input' => 'multiselect',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => '',
                    'group' => 'Area',
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
        }
        endif;
    }

}


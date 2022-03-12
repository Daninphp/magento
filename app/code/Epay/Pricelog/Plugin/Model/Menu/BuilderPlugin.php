<?php
namespace Epay\Pricelog\Plugin\Model\Menu;


use Magento\Backend\Model\Menu\Builder;
use Magento\Backend\Model\Menu;
use Magento\Backend\Model\Menu\ItemFactory;
use Epay\Pricelog\Helper\Config;

class BuilderPlugin
{
    /**
     * @var ItemFactory
     */
    private $menuItemFactory;

    /**
     * @var Config
     */
    private $_configHelper;

    const MAGENTO_ROOT_PARENT = 'Magento_Reports::report';

    const PRICELOG_PARENT = 'Epay_Pricelog::pricelog';

    const PRICELOG_RESOURCE = self::PRICELOG_PARENT;

    const PRICELOG_MODULE = 'Epay_Pricelog';

    const ACTION = 'pricelog';

    /**
     * BuilderPlugin constructor.
     * @param ItemFactory $menuItemFactory
     * @param Config $configHelper
     */
    public function __construct(
        ItemFactory $menuItemFactory,
        Config $configHelper
    ) {
        $this->menuItemFactory = $menuItemFactory;
        $this->_configHelper = $configHelper;
    }


    /**
     * @param Builder $subject
     * @param Menu $menu
     * @return Menu
     */
    public function afterGetResult(Builder $subject, Menu $menu)
    {
        if ($this->_configHelper->isEnabled() && $this->_configHelper->isGridEnabled()) {
            /** @var Menu\Item $item */
            $item = $this->menuItemFactory->create([
                'data' => [
                    'parent_id' => self::MAGENTO_ROOT_PARENT,
                    'id'        => self::PRICELOG_PARENT,
                    'title'     => 'Price Changes Log',
                    'module'    => self::PRICELOG_MODULE,
                    'resource'  => self::PRICELOG_RESOURCE,
                    'sortOrder' => '900'
                ]
            ]);
            $menu->add($item, self::MAGENTO_ROOT_PARENT);

            ///add submenu for the menu item added above
            $item = $this->menuItemFactory->create([
                'data' => [
                    'parent_id' => self::PRICELOG_PARENT,
                    'id'        => self::PRICELOG_PARENT . '_grid',
                    'title'     => 'Price Log Grid',
                    'module'    => self::PRICELOG_MODULE,
                    'resource'  => self::PRICELOG_RESOURCE,
                    'action'    => self::ACTION
                ]
            ]);
            $menu->add($item, 'Epay_Pricelog::pricelog');
        }

        return $menu;
    }
}

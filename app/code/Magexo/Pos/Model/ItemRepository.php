<?php

namespace Magexo\Pos\Model;

use Magexo\Pos\Api\ItemRepositoryInterface;
use Magexo\Pos\Model\ResourceModel\Item\CollectionFactory;

class ItemRepository implements ItemRepositoryInterface
{
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function getList()
    {
        $this->collectionFactory->create()->getItems();
    }
}


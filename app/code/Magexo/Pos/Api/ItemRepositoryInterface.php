<?php

namespace Magexo\Pos\Api;

interface ItemRepositoryInterface
{
    /**
     * @return \Magexo\Pos\Api\Data\ItemInterface[]
     */
    public function getList();
}

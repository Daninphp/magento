<?php

namespace Magexo\Pos\Api\Data;

interface ItemInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string|null
     */
    public function getDescription();
}

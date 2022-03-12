<?php

namespace Magexo\Pos\Model\Pos;

use Magexo\Pos\Model\ResourceModel\Pos\Collection;
use Magexo\Pos\Model\Pos as PosCreate;


class Pos
{
    private $collection;
    private $create_pos;

    public function __construct(
        Collection $collection,
        PosCreate $create_pos
    )
    {
        $this->collection = $collection;
        $this->create_pos = $create_pos;
    }

    public function getPosList()
    {
        return $this->collection;
    }

    public function deleteProduct($id)
    {
        try {
            $this->collection->addFieldToFilter('pos_id', $id)->getFirstItem()->delete();
            echo json_encode('Product successfully deleted');
        } catch (\Exception $exception){
            echo json_encode($exception->getMessage());
        }
    }

    public function getPosData($id)
    {
        try {
            echo json_encode($this->collection->addFieldToFilter('pos_id', $id)->getFirstItem()->getData());
        } catch (\Exception $exception){
            echo json_encode($exception->getMessage());
        }
    }

    public function updatePos($data)
    {
        try {
            $this->collection
                ->addFieldToFilter('pos_id', $data['pos_id'])
                ->getFirstItem()
                ->setName($data['name'])
                ->setAddress($data['address'])
                ->setIsAvailable($data['availability'])
                ->save()
            ;
            echo json_encode('Product Updated!');
        } catch (\Exception $exception){
            echo json_encode($exception->getMessage());
        }
    }

    public function createPos($data)
    {
        try {
            $this->create_pos->setPosId($data['pos_id']);
            $this->create_pos->setName($data['name']);
            $this->create_pos->setAddress($data['address']);
            $this->create_pos->setIsAvailable($data['availability']);
            $this->create_pos->save();
            echo json_encode('Product Created!');
        } catch (\Exception $exception){
            echo json_encode($exception->getMessage());
        }
    }

}

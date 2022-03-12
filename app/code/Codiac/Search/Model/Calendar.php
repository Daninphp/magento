<?php

namespace Codiac\Search\Model;

use Codiac\Search\Model\ResourceModel\Exclusion\CollectionFactory as ExclusionCollection;
use Codiac\Search\Model\ExclusionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;

class Calendar
{

    private $exclusionCollection;
    private $exclusionFactory;
    private $storeManager;
    private $logger;
    private $connection;
    private $resource;


    public function __construct(
        ExclusionCollection $exclusionCollection,
        ResourceConnection $resource,
        LoggerInterface $logger,
        ExclusionFactory $exclusionFactory,
        StoreManagerInterface $storeManager
    )
    {
        $this->exclusionCollection = $exclusionCollection;
        $this->logger = $logger;
        $this->exclusionFactory = $exclusionFactory;
        $this->storeManager = $storeManager;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
    }

    private function getTableModel()
    {
        return $this->exclusionFactory->create();
    }

    public function insertDate(string $date = null)
    {
        $model = $this->exclusionFactory->create();

        try {
            $model->setExclusionDate($date);
            $model->save();
            return ['status'=> 'success', 'message' => "Datum wurde zur Ausnahmeliste hinzugefügt!"];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return ['status'=> 'error', 'message' => "Beim Einfügen des Datums ist ein Problem aufgetreten. Bitte versuchen Sie es erneut, oder es liegt ein anderes Datum vor!"];
        }
    }

    public function getAllDates()
    {
        return $this->exclusionCollection->create()->setOrder('exclusion_date', 'ASC')->getData();
    }

    public function deleteDate(string $id)
    {
        try {
            $this->exclusionFactory->create()->load($id)->delete();
            return ['status'=> 'success','message' => "Datum wurde erfolgreich gelöscht!"];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return ['status'=> 'error','message' => "Beim Löschen des Datums ist ein Problem aufgetreten. Überprüfen Sie das Protokoll auf weitere Informationen!"];
        }
    }

    public function deleteAllRecords()
    {
        $sql = "TRUNCATE calendar_exclusion_days";
        $this->connection->query($sql);
    }

}

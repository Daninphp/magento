<?php
namespace Codiac\Search\Model;

use Codiac\Search\Model\ResourceModel\Locator\CollectionFactory as LocatorCollection;
use Magento\Catalog\Model\Session;

class GpsLocation
{
    private $locatorCollection;
    private $_session;

    public function __construct(LocatorCollection $locatorCollection, Session $session)
    {
        $this->locatorCollection = $locatorCollection;
        $this->_session = $session;
    }

    public function getNearestPlaceAvailableForShipping(array $data) : string
    {
        $fullLocations = $this->locatorCollection->create()->addFieldToFilter('active', 1);

        $distances = [];
        foreach ($fullLocations as $location) {
            $distance = $this->distance($data['latitude'], $data['longitude'], $location->getLatitude(), $location->getLongitude());
            $distances[] = [
                'place' => $location->getLabel(),
                'ltd' => $location->getLatitude(),
                'lng' => $location->getLongitude(),
                'distance' => $distance,
            ];
        }

        $min = min(array_column($distances, 'distance'));

        $key = array_search($min, array_column($distances, 'distance'));

        return $distances[$key]['place'];
    }

    private function distance($lat1, $lng1, $lat2, $lng2)
    {
        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lng1 *= $pi80;
        $lat2 *= $pi80;
        $lng2 *= $pi80;

        $r = 6372.797; // mean radius of Earth in km
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        return $km;
    }

    public function setLongitudeLatitude($postalCode)
    {

    }

}
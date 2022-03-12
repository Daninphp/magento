<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);

use Magento\Framework\App\Bootstrap;
require 'app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$objectManager = $bootstrap->getObjectManager();
use Smartweb\Auto\Helper\Data;
use Smartweb\Auto\Model\Data as Model;

$model = new Model();
$model->getModels();


$attributeNames = 
    [
    'Marka automobila'  =>
        [
            'Alfa' => ['id' => Data::AUDI_ID],
            'Audi' => ['id' => Data::ALFA_ID]
        ],
    'Model automobila'  =>
        [
            'Alfa 147'  => ['id' => Data::ALFA_147],
            'Audi A4'   => ['id' => Data::AUDI_A4],
            'Audi A6'   => ['id' => Data::AUDI_A6]
        ],
    'Tip automobila'    =>
        [
            'Audi Estate' => ['id' => Data::AUDI_ESTATE],
            'Audi Salon'  => ['id' => Data::AUDI_SALOON],
            'Alfa Salon'  => ['id' => Data::ALFA_SALON]
        ],
    'Vrsta sijalice'    =>
        [
            'migavac zadnji'    => ['id' => Data::MIGAVAC_Z],
            'migavac prednji'   => ['id' => Data::MIGAVAC_P],
            'stop svetlo'       => ['id' => Data::STOP_S],
            'far'               => ['id' => Data::FAR]
        ]
];


foreach ($attributeNames as $key => $attributeName) {
    if ($key == 'Model automobila') {
        foreach ($attributeNames[$key] as $innerKey => $value) {
            if (strpos($innerKey, 'Audi') !== false) {
                $data[] = [
                    'mark_id' => DATA::AUDI_ID,
                    'model_id' => $value['id'],
                    'mark_name' => $innerKey
                ];
            } else if (strpos($innerKey, 'Alfa') !== false) {
                $data[] = [
                    'mark_id' => DATA::ALFA_ID,
                    'model_id' => $value['id'],
                    'mark_name' => $innerKey
                ];
            }
        }
    }
}
echo'<pre>';print_r($data);
die();




$string = '';
//$attributeValues = implode(',', $attributeNames['Marka automobila']);
$endElement = end($attributeNames['Marka automobila']);
foreach ($attributeNames['Marka automobila'] as $attributeValue) {
    if ($attributeValue !== $endElement) {
        $string .= $attributeValue . ', ';
    } else {
        $string .= $attributeValue;
    }
}
//echo $string;die();
$options = ['attribute_id' => null, 'values' => $attributeNames['Marka automobila']];

var_dump($options);

echo'<pre>';print_r($attributeNames);

foreach ($attributeNames as $key => $attributeName) {
    $attributes[] = [
        'code' => strtolower(str_replace(' ', '_', $key)),
        'label' => $key
    ];
}

echo'<pre>';print_r($attributes);
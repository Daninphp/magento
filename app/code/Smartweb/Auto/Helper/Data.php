<?php

namespace Smartweb\Auto\Helper;

class Data
{

    const MARKA_ATTR_CODE   = 'marka_automobila';
    const MODEL_ATTR_CODE   = 'model_automobila';
    const TYPE_ATTR_CODE    = 'tip_automobila';
    const BULB_TYPE_CODE    = 'vrsta_sijalice';

    const AUDI_ID = 1;
    const ALFA_ID = 2;

    const ALFA_147  = 21;
    const AUDI_A4   = 11;
    const AUDI_A6   = 12;

    const ALFA_SALON    = 211;
    const AUDI_ESTATE   = 111;
    const AUDI_SALOON   = 112;

    const MIGAVAC_Z = 510;
    const MIGAVAC_P = 511;
    const STOP_S    = 512;
    const FAR       = 513;


    /**
     * @param false $returnUpgrade
     * @return \int[][][]|\string[][]
     */
    public static function getInstallData($returnUpgrade = false): array
    {
        if ($returnUpgrade) {
            return [
                'Marka automobila'  =>
                    [
                        'Alfa' => ['id' => self::AUDI_ID],
                        'Audi' => ['id' => self::ALFA_ID]
                    ],
                'Model automobila'  =>
                    [
                        'Alfa 147'  => ['id' => self::ALFA_147],
                        'Audi A4'   => ['id' => self::AUDI_A4],
                        'Audi A6'   => ['id' => self::AUDI_A6]
                    ],
                'Tip automobila'    =>
                    [
                        'Audi Estate' => ['id' => self::AUDI_ESTATE],
                        'Audi Salon'  => ['id' => self::AUDI_SALOON],
                        'Alfa Salon'  => ['id' => self::ALFA_SALON]
                    ],
                'Vrsta sijalice'    =>
                    [
                        'migavac zadnji'    => ['id' => self::MIGAVAC_Z],
                        'migavac prednji'   => ['id' => self::MIGAVAC_P],
                        'stop svetlo'       => ['id' => self::STOP_S],
                        'far'               => ['id' => self::FAR]
                    ]
            ];
        }

        return [
            'Marka automobila'  => ['Alfa','Audi'],
            'Model automobila'  => ['Alfa 147','Audi A4','Audi A6'],
            'Tip automobila'    => ['Audi Estate','Audi Salon','Alfa Salon'],
            'Vrsta sijalice'    => ['migavac zadnji','migavac prednji','stop svetlo','far']
        ];
    }
}
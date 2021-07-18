<?php


namespace AlegzaCRM\AlegzaAPI\Models;

/**
 * Class AlegzaModel
 *
 * Dinamikus modellek az Alegza API-hoz
 *
 * @package AlegzaCRM\AlegzaAPI\Models
 */
class AlegzaModel
{

    /**
     * $data tömb lehet, amivel feltölti az osztályt
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        if (!is_null($data))
        {
            $this->create($data);
        }
    }

    /**
     * Feltölti az osztályt a $data asszociatív tömbben lévő elemekkel.
     * @param $data
     */
    public function create($data) {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }

    /**
     * Az objektumot kulcs-érték formájú tömbként adja vissza.
     * @return array
     */
    public function asArray() : array
    {
        return json_decode(json_encode($this), true);
    }
}
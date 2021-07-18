<?php


namespace AlegzaCRM\AlegzaAPI\Models;


class Product extends AlegzaModel
{
    public $id;
    public $created_at;
    public $updated_at;
    public $name;
    public $type;
    public $provider;
    public $description;
    public $deleted_at;
    public $value;
}
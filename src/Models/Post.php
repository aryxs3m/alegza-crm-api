<?php


namespace AlegzaCRM\AlegzaAPI\Models;


class Post extends AlegzaModel
{
    public $id;
    public $created_at;
    public $updated_at;
    public $person;
    public $type;
    public $post_timestamp;
    public $message;
    public $success;
    public $deleted_at;
    public $user_id;
}
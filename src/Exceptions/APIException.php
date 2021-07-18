<?php

namespace AlegzaCRM\AlegzaAPI\Exceptions;

use Exception;

class APIException extends Exception
{
    private $errors;

    /**
     * APIException constructor.
     * @param array|null $errors
     */
    public function __construct(string $message, array $errors = null)
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
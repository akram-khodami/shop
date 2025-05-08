<?php

namespace App\Exceptions;

use App\Exceptions\ApiException;
use Illuminate\Database\QueryException;

class MyApiQueryException extends ApiException
{
    public function __construct($message, ?QueryException $e = null, int $statusCode = 500)
    {
        parent::__construct($message, $e, $statusCode);
    }
}

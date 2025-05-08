<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $data;
    protected $statusCode;

    public function __construct($message, ?Exception $e = null, int $statusCode = 500)
    {
        parent::__construct($message, 0, $e);

        $this->statusCode = $statusCode;
        $this->data = [
            'exception' => $e ? get_class($e) : null,
            'trace' => config('app.debug') && $e ? $e->getTrace() : null,
        ];
    }

    public function render($request)
    {
        return response()->json(
            [
                'success' => false,
                'message' => config('app.debug') ? $this->getMessage() : null,
                'errors' => config('app.debug') ? $this->getMessage() : null,
                'data' => config('app.debug') ? $this->data : null,
            ], $this->statusCode);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}

<?php


namespace App\Services;


interface PayServiceInterface
{

    public function sendRequest(array $data);

    public function verify(array $data);

}

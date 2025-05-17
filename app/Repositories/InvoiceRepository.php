<?php


namespace App\Repositories;


use App\Models\Invoice;

class InvoiceRepository extends BaseRepository
{

    protected function model(): string
    {
        return Invoice::class;
    }

}

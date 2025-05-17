<?php


namespace App\Repositories;


use App\Models\Transaction;

class TransactionRepository extends BaseRepository
{

    protected function model(): string
    {
        return Transaction::class;
    }
}

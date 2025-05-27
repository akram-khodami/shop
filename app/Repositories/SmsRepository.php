<?php


namespace App\Repositories;


use App\Models\Sms;
use Illuminate\Database\Eloquent\Model;

class SmsRepository extends BaseRepository
{

    protected function model(): string
    {
        return Sms::class;
    }

    public function getMobileLastCode(string $mobile): ?Model
    {
        $sms = Sms::where('receptor', $mobile)
            ->latest()
            ->first();

        return $sms;
    }

}

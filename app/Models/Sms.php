<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $fillable = [
        'code',
        'receptor',
        'expired_at',
        'serviceName',
        'serviceType',
        'used',
        'mobile',
        'ip'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'used' => 'boolean',
        'mobile' => 'string',
    ];

    public static function generateCode()
    {
        do {

            $code = str_pad(random_int(1000, 999999), 6, '0', STR_PAD_LEFT);

        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function markAsUsed()
    {
        $this->update(['used' => true]);
    }

}

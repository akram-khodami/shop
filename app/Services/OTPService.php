<?php


namespace App\Services;


use App\Exceptions\ApiException;
use App\Models\Sms;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OTPService
{
    private $ApiKey;

    public function __construct()
    {
        $this->ApiKey = env('OTP_API_KEY', '');
    }

    public function sendSms(int $mobile)
    {
        $code = Sms::generateCode();

        $this->storeCode($code, $mobile);

        $postField = [
            'receptor' => $mobile,
            'token' => $code,
            'template' => 'loginTemplate',//config
            'type' => 'sms',

        ];


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.kavenegar.com/v1/{$this->ApiKey}/verify/lookup.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postField),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function checkCode(array $data)
    {
        $sms = Sms::where('code', $data['code'])
            ->where('receptor', $data['mobile'])
            ->latest()
            ->first();

        if (!$sms) {

            throw new ApiException('Code is incorrect', $data, 404);

        }

        if (Carbon::parse($sms->expired_at)->isPast()) {

            throw new ApiException('Code is expired', $data, 422);

        }

        $user = User::firstOrCreate(
            ['mobile' => $data['mobile']],
            [
                'name' => $data['mobile'], // یا نام دیگری اگر دارید
                'password' => Hash::make($data['mobile']),
            ]
        );

        return $user;
    }

    public function storeCode(int $code, int $mobile)
    {

        Sms::create(
            [
                'code' => $code,
                'receptor' => $mobile,
                'expired_at' => Carbon::now()->addMinutes(5), // 5 دقیقه اعتبار,
                'serviceName' => 'kavenegar',
                'serviceType' => 'sms',
            ]);
    }

}

<?php


namespace App\Services;


use App\Exceptions\ApiException;
use App\Models\Sms;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use function Illuminate\Database\Query\update;

class OTPService
{
    protected $ApiKey;

    public function __construct()
    {
        $this->ApiKey = env('OTP_API_KEY', '');
    }

    public function sendSms(string $mobile, bool $resend = false)
    {
        $cacheKey = "login_attempts_" . $mobile;

        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 5) {

            throw new ApiException('تعداد تلاش‌های شما بیش از حد مجاز است. لطفاً ۱۵ دقیقه دیگر مجدداً تلاش کنید.', [$mobile], 429);

        }

        //===check sms is sent
        $sms = Sms::where('receptor', $mobile)
            ->latest()
            ->first();

        if ($sms) {//sms is sent in adavance

            if (!Carbon::parse($sms->expired_at)->isPast() && $sms->usef == false) {

                if ($resend == false) {

                    throw new ApiException('sms is sent.enter the code and log in or resend request for gestting new code', [$mobile], 201);

                } else {

                    //expire old code
                    Sms::where('receptor', $mobile)->where('used', false)->update(['expired_at' => now()]);//expired_at should not come to condition?

                }

            }

        }


        $code = Sms::generateCode();

        $sms = $this->storeCode($code, $mobile);

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

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($httpCode !== 200) {

            $sms->update(['expired_at' => now()]);

            throw new ApiException('Failed to send SMS', [$response, $httpCode], 500);

        }

        return $response;

    }

    public function checkCode(array $data)
    {
        $cacheKey = "login_attempts_" . $data['mobile'];

        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 5) {

            throw new ApiException('تعداد تلاش‌های شما بیش از حد مجاز است. لطفاً ۱۵ دقیقه دیگر مجدداً تلاش کنید.', $data, 429);

        }

        $sms = Sms::where('code', $data['code'])
            ->where('receptor', $data['mobile'])
            ->latest()
            ->first();

        if (!$sms) {

            throw new ApiException('Code is incorrect', $data, 404);

        }

        if (Carbon::parse($sms->expired_at, config('app.timezone'))->isPast()) {

            Cache::put($cacheKey, $attempts + 1, now()->addMinutes(15));

            throw new ApiException('Code is expired', [$sms], 422);

        }

        if ($sms->used) {

            throw new ApiException('Code is used', $data, 422);

        }

        $user = User::firstOrCreate(
            ['mobile' => $data['mobile']],
            [
                'name' => $data['mobile'],
                'password' => Hash::make($data['mobile']),
            ]
        );

        $sms->markAsUsed();

        Cache::forget($cacheKey);

        return $user;
    }

    public function storeCode(string $code, string $mobile)
    {

        $sms = Sms::create(
            [
                'code' => $code,
                'receptor' => strval($mobile),
                'expired_at' => Carbon::now()->addMinutes(5), // 2 دقیقه اعتبار,
                'serviceName' => 'kavenegar',
                'serviceType' => 'sms',
            ]);

        return $sms;
    }

}

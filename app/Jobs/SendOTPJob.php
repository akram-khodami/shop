<?php

namespace App\Jobs;

use App\Exceptions\ApiException;
use App\Repositories\SmsRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Hash;
use Kavenegar\KavenegarApi;
use App\Enums\SmsStatus;

class SendOTPJob implements ShouldQueue
{
    use Queueable;

    protected $code;
    protected $mobile;

    /**
     * Create a new job instance.
     */
    public function __construct(string $mobile, string $code)
    {
        $this->mobile = $mobile;
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $smsRepository = app(SmsRepository::class);

        $apiKey = env('KAVENEGAR_API_KEY', 'api-key-sandbox');
        $template = env('KAVENEGAR_OTP_TEMPLATE', 'otp-default');

        $sms = $smsRepository->create(
            [
                'code' => Hash::make($this->code),
                'receptor' => $this->mobile,
                'expired_at' => Carbon::now()->addMinutes(5),
                'serviceName' => 'kavenegar',
                'serviceType' => 'sms',
                'used' => false,
                'status' => SmsStatus::PENDING,
            ]);

        try {

            $api = new KavenegarApi($apiKey);

            $api->VerifyLookup($this->mobile, $this->code, null, null, $template);

            $sms->update(['expired_at' => now(), 'status' => SmsStatus::Success]);

        } catch (\Kavenegar\Exceptions\ApiException $e) {
            // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد
            $sms->update(['expired_at' => now(), 'status' => SmsStatus::FAILED]);

            throw new ApiException('Failed to send SMS', [$e->getMessage()], 501);

        } catch (\Kavenegar\Exceptions\HttpException $e) {
            // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
            $sms->update(['expired_at' => now(), 'status' => SmsStatus::FAILED]);

            throw new ApiException('Failed to send SMS', [$e->getMessage()], 500);
        }


    }
}

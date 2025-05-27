<?php


namespace App\Services;


use App\Exceptions\ApiException;
use App\Jobs\SendOTPJob;
use App\Models\Sms;
use App\Repositories\SmsRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Enums\SmsStatus;

class OTPService
{
    protected $attempts;
    protected $cacheKey;
    protected $smsRepository;
    protected $userRepository;

    public function __construct(SmsRepository $smsRepository, UserRepository $userRepository)
    {
        $this->cacheKey = NULL;
        $this->smsRepository = $smsRepository;
        $this->userRepository = $userRepository;
    }

    public function sendSms(string $mobile, bool $resend = false)
    {
        $this->controlAttempts($mobile);

        $sms = $this->smsRepository->getMobileLastCode($mobile);

        if ($sms && !$sms->isExpired() && !$sms->used && $sms->status == SmsStatus::Success) {

            if ($resend) {

                $sms->update(['expired_at' => Carbon::now()]);

            } else {

                throw new ApiException('SMS already sent', [$mobile], 201);

            }

        }

        $code = Sms::generateCode();

        $this->increaseAttempts();

        dispatch_sync(new SendOTPJob($mobile, $code));

    }

    public function checkCode(array $data)
    {
        $this->controlAttempts($data['mobile']);

        $sms = $this->smsRepository->getMobileLastCode($data['mobile']);

        if (!$sms) {

            throw new ApiException('Code not found', ['mobile' => $data['mobile']], 404);

        }

        if (!Hash::check($data['code'], $sms->code)) {

            $this->increaseAttempts(); // Increment attempts on failed validation

            throw new ApiException('Invalid code', ['mobile' => $data['mobile']], 422);

        }

        if ($sms->isExpired()) {

            throw new ApiException('Code has expired', ['mobile' => $data['mobile']], 422);

        }

        if ($sms->used) {

            throw new ApiException('Code already used', ['mobile' => $data['mobile']], 422);
        }

        // Create user if doesn't exist (first-time login/registration)
        $user = $this->userRepository->firstOrCreate(['mobile' => $data['mobile']],
            [
                'name' => 'User_' . substr($data['mobile'], -4),
                'mobile' => $data['mobile'],
                'email' => $data['mobile'] . '@temp.local', // Unique temporary email
                'password' => Hash::make(Str::random(16)),
            ]);

        $sms->markAsUsed();

        Cache::forget($this->cacheKey);

        return $user;
    }

    private function controlAttempts(string $mobile)
    {
        $this->cacheKey = "login_attempts_" . $mobile;

        $this->attempts = Cache::get($this->cacheKey, 0);

        if ($this->attempts >= 5) {

            throw new ApiException('You are attempting a lot. Try again after 15 minutes.', [$mobile], 429);

        }

    }

    private function increaseAttempts(): void
    {
        Cache::put($this->cacheKey, $this->attempts + 1, now()->addMinutes(15));//???
    }

}

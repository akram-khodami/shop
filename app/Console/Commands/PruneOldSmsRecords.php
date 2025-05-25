<?php

namespace App\Console\Commands;

use App\Models\Sms;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PruneOldSmsRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-old-sms-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Sms::where('expired_at', '<', Carbon::now())->delete();

        $this->info("Deleted {$count} expired SMS records.");
    }
}

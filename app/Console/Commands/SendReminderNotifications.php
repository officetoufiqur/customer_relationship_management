<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Notifications\SendExpiryNotifications;
use Illuminate\Console\Command;

class SendReminderNotifications extends Command
{
    protected $signature = 'send:expires-reminder';

    protected $description = 'Send expiry reminders automatically';

    public function handle()
    {
        $targetDate = now()->addDay()->toDateString();
        $currentDate = now()->toDateString();

        $companies = Company::whereDate('expiry_date', $targetDate)->get();
        $experiedCompanies = Company::whereDate('expiry_date', $currentDate)->get();

        foreach ($companies as $company) {
            $company->notify(
                new SendExpiryNotifications("Reminder: {$company->name} expires tomorrow.", $company)
            );
            $company->renewal_status = 'expiring_soon';
            $company->save();
        }

        foreach ($experiedCompanies as $company) {
            $company->renewal_status = 'expired';
            $company->save();
        }

        $this->info('Notifications sent: '.$companies->count());
    }
}

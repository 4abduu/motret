<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubscriptionSystem;
use App\Models\SubscriptionCombo;
use App\Models\SubscriptionUser;
use Carbon\Carbon;
use App\Models\User;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and update user roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredSubsriptionsSystem = SubscriptionSystem::where('end_date', '<', Carbon::now())->get();
        $expiredSubscriptionsUser = SubscriptionUser::where('end_date', '<', Carbon::now())->get();
        $expiredSubscriptionsCombo = SubscriptionCombo::where('end_date', '<', Carbon::now())->get();
    
        foreach ($expiredSubsriptionsSystem as $subscriptionSystem) {
            $user = User::find($subscriptionSystem->user_id);
            $user->role = 'user';
            $user->save();
            $this->info('Updated user role to user for user ID: ' . $user->id);
        }

        $this->info('Expired subscriptions check completed.');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tenant\Business;
use App\Models\Tenant\Subscription;
use App\Models\Tenant\Counter;
use App\Models\Queue\QueueEntry;
use App\Models\Marketing\LoyaltyReward;
use App\Models\Marketing\CustomerFeedback;
use App\Enums\SubTier;
use App\Enums\QueueStatus;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class MonthlyBusinessSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Create/Update the Monthly Owner
        $owner = User::updateOrCreate(
            ['email' => 'monthly@qline.local'],
            [
                'name' => 'Monthly Shop Owner',
                'password' => Hash::make('password'),
                'role' => 'business_owner',
                'email_verified_at' => now(),
                'profile_completed' => true,
            ]
        );

        // 2. Create/Update the Monthly Business
        $business = Business::updateOrCreate(
            ['slug' => 'local-cafe'],
            [
                'user_id' => $owner->id,
                'name' => 'The Local Cafe',
                'join_code' => 'CAFE',
                'queue_prefix' => 'C',
                'queue_status' => 'open',
                'address' => '45 Artisan Street, George Town',
                'daily_limit' => 500,
            ]
        );

        // Link owner back to business
        $owner->update(['business_id' => $business->id]);

        // 3. Ensure Monthly Subscription
        Subscription::updateOrCreate(
            ['business_id' => $business->id],
            [
                'type' => SubTier::MONTHLY,
                'status' => 'active',
                'starts_at' => now()->subWeeks(2),
                'expires_at' => now()->addWeeks(2),
            ]
        );

        // 4. Create Single Default Counter (Standard for Monthly)
        $business->counters()->delete();
        $counter = Counter::create([
            'business_id' => $business->id,
            'name' => 'Counter 1',
            'is_active' => true,
        ]);

        // 5. Create some Loyalty Rewards
        $business->loyaltyRewards()->delete();
        LoyaltyReward::create([
            'business_id' => $business->id,
            'reward_type' => 'freebie',
            'reward_value' => 'Free Coffee',
            'required_visits' => 10,
            'is_active' => true,
        ]);

        // 6. Create Queue History
        $business->queueEntries()->delete();
        for ($q = 1; $q <= 12; $q++) {
            $status = $faker->randomElement(['completed', 'skipped', 'waiting']);
            $wa_id = '+601' . $faker->randomNumber(8, true);
            
            $entry = QueueEntry::create([
                'business_id' => $business->id,
                'ticket_number' => $q,
                'ticket_code' => 'C-' . str_pad($q, 3, '0', STR_PAD_LEFT),
                'wa_id' => $wa_id,
                'source' => 'whatsapp',
                'status' => $status,
                'counter_id' => in_array($status, ['completed', 'skipped']) ? $counter->id : null,
                'called_at' => $status === 'completed' ? now()->subMinutes(rand(10, 60)) : null,
                'created_at' => now()->subMinutes(rand(60, 180)),
            ]);

            if ($status === 'completed' && $faker->boolean(40)) {
                CustomerFeedback::create([
                    'business_id' => $business->id,
                    'queue_entry_id' => $entry->id,
                    'wa_id' => $wa_id,
                    'rating' => rand(3, 5),
                    'comment' => $faker->sentence(),
                ]);
            }
        }

        $this->command->info('Monthly business seeded! Login with: monthly@qline.local / password');
    }
}

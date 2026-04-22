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

class AdvancedBusinessSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Create/Update the Power Owner
        $owner = User::updateOrCreate(
            ['email' => 'advanced@qline.local'],
            [
                'name' => 'Advanced Shop Owner',
                'password' => Hash::make('password'),
                'role' => 'business_owner',
                'email_verified_at' => now(),
                'profile_completed' => true,
            ]
        );

        // 2. Create/Update the Advanced Business
        $business = Business::updateOrCreate(
            ['slug' => 'elite-clinic'],
            [
                'user_id' => $owner->id,
                'name' => 'Elite Clinic & Pharmacy',
                'join_code' => 'ELIT',
                'queue_prefix' => 'E',
                'queue_status' => 'open',
                'address' => '123 Premium Way, Kuala Lumpur',
                'daily_limit' => 0, // Unlimited
            ]
        );

        // Link owner back to business (Critical for QueueDashboard)
        $owner->update(['business_id' => $business->id]);

        // 3. Ensure Subscription
        Subscription::updateOrCreate(
            ['business_id' => $business->id],
            [
                'type' => SubTier::ADVANCED,
                'status' => 'active',
                'starts_at' => now()->subMonth(),
                'expires_at' => now()->addMonth(),
            ]
        );

        // 4. Create Multiple Counters (clean up old ones first if needed)
        $business->counters()->delete();
        $counters = [];
        $counterNames = ['Registration', 'Consultation A', 'Consultation B', 'Pharmacy', 'Payment'];
        foreach ($counterNames as $name) {
            $counters[] = Counter::create([
                'business_id' => $business->id,
                'name' => $name,
                'is_active' => true,
            ]);
        }

        // 5. Create some Staff
        for ($i = 1; $i <= 3; $i++) {
            User::updateOrCreate(
                ['email' => "staff$i@elite.local"],
                [
                    'name' => "Staff $i",
                    'password' => Hash::make('password'),
                    'role' => 'business_staff',
                    'business_id' => $business->id,
                    'email_verified_at' => now(),
                    'profile_completed' => true,
                ]
            );
        }

        // 6. Create some Loyalty Rewards
        $business->loyaltyRewards()->delete();
        LoyaltyReward::create([
            'business_id' => $business->id,
            'reward_type' => 'freebie',
            'reward_value' => 'Free Consultation',
            'required_visits' => 5,
            'is_active' => true,
        ]);

        // 7. Create Recent Queue History (clean up old entries to keep it fresh)
        $business->queueEntries()->delete();
        for ($q = 1; $q <= 20; $q++) {
            $status = $faker->randomElement(['completed', 'skipped', 'cancelled', 'serving', 'called', 'waiting']);
            $counter = null;
            
            if (in_array($status, ['serving', 'called', 'completed'])) {
                $counter = $faker->randomElement($counters);
            }

            $wa_id = $faker->boolean(80) ? '+601' . $faker->randomNumber(8, true) : null;
            
            $entry = QueueEntry::create([
                'business_id' => $business->id,
                'ticket_number' => $q,
                'ticket_code' => 'E-' . str_pad($q, 3, '0', STR_PAD_LEFT),
                'wa_id' => $wa_id,
                'source' => $wa_id ? 'whatsapp' : 'Anonymous',
                'status' => $status,
                'counter_id' => $counter?->id,
                'called_at' => in_array($status, ['called', 'serving', 'completed']) ? now()->subMinutes(rand(5, 30)) : null,
                'created_at' => now()->subMinutes(rand(30, 120)),
            ]);

            // Add some feedback for completed entries
            if ($status === 'completed' && $faker->boolean(60)) {
                CustomerFeedback::create([
                    'business_id' => $business->id,
                    'queue_entry_id' => $entry->id,
                    'wa_id' => $wa_id ?? 'Anonymous',
                    'rating' => rand(4, 5),
                    'comment' => $faker->sentence(),
                ]);
            }
        }

        $this->command->info('Advanced business seeded! Login with: advanced@qline.local / password');
    }
}

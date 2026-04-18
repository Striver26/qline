<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tenant\Business;
use App\Models\Tenant\Subscription;
use App\Models\Tenant\Payment;
use App\Models\Queue\QueueEntry;
use App\Models\Marketing\WhatsappMessage;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class AdminDumpDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Let's create 15 Businesses and their Owners
        for ($i = 0; $i < 15; $i++) {
            // Create Owner
            $owner = User::create([
                'name' => 'Owner ' . $faker->firstName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'role' => 'business_owner',
                'email_verified_at' => now(),
                'profile_completed' => true,
                'created_at' => now()->subDays(rand(5, 60)),
            ]);

            // Create Business
            $business = Business::create([
                'user_id' => $owner->id,
                'name' => $faker->company,
                'slug' => Str::slug($faker->unique()->company . '-' . Str::random(4)),
                'join_code' => strtoupper($faker->bothify('?#?#')),
                'queue_prefix' => strtoupper($faker->bothify('?')),
                'queue_status' => $faker->randomElement(['open', 'closed', 'paused']),
                'address' => $faker->address,
                'created_at' => $owner->created_at,
            ]);

            // Create Subscription
            Subscription::create([
                'business_id' => $business->id,
                'type' => $faker->randomElement(['daily', 'monthly']),
                'status' => $faker->randomElement(['active', 'expired', 'cancelled']),
                'starts_at' => now()->subDays(rand(1, 30)),
                'expires_at' => now()->addDays(rand(10, 60)),
            ]);

            // Create Payments
            for ($p = 0; $p < rand(1, 4); $p++) {
                Payment::create([
                    'business_id' => $business->id,
                    'amount' => $faker->randomFloat(2, 10, 250),
                    'currency' => 'MYR',
                    'method' => $faker->randomElement(['fpx', 'card', 'wallet']),
                    'status' => $faker->randomElement(['paid', 'pending', 'failed']),
                    'reference' => 'REF-' . strtoupper(Str::random(8)),
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }

            // Create Queue Entries
            $queueCount = rand(5, 25);
            for ($q = 1; $q <= $queueCount; $q++) {
                $status = $faker->randomElement(['waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled']);

                $wa_id = $faker->boolean(70) ? '+601' . $faker->randomNumber(8, true) : null;
                $source = $wa_id ? 'whatsapp' : 'Anonymous';

                $entry = QueueEntry::create([
                    'business_id' => $business->id,
                    'ticket_number' => $q,
                    'ticket_code' => $business->queue_prefix . '-' . str_pad($q, 3, '0', STR_PAD_LEFT),
                    'wa_id' => $wa_id,
                    'source' => $source,
                    'status' => $status,
                    'created_at' => now()->subMinutes(rand(10, 300)),
                ]);

                // Create WhatsApp Message logs if they joined via whatsapp
                if ($wa_id) {
                    WhatsappMessage::create([
                        'business_id' => $business->id,
                        'queue_entry_id' => $entry->id,
                        'wa_id' => $wa_id,
                        'message_id' => 'MSG-' . Str::random(10),
                        'direction' => 'outbound',
                        'template' => 'welcome_queue',
                        'body' => 'Welcome to ' . $business->name . '! Your ticket is ' . $entry->ticket_code . '.',
                        'status' => 'delivered',
                        'created_at' => now()->subMinutes(rand(10, 300)),
                    ]);

                    if (in_array($status, ['called', 'serving'])) {
                        WhatsappMessage::create([
                            'business_id' => $business->id,
                            'queue_entry_id' => $entry->id,
                            'wa_id' => $wa_id,
                            'message_id' => 'MSG-' . Str::random(10),
                            'direction' => 'outbound',
                            'template' => null,
                            'body' => "It's your turn! Please proceed to the counter.",
                            'status' => 'read',
                            'created_at' => now()->subMinutes(rand(1, 10)),
                        ]);
                    }
                }
            }
        }
    }
}

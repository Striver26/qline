<?php
$files = glob(__DIR__ . '/database/migrations/*.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($file, 'create_businesses_table') !== false) {
        $schema = "            \$table->id();
            \$table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            \$table->string('name')->default('New Business');
            \$table->string('slug')->unique();
            \$table->string('join_code')->unique();
            \$table->string('phone')->nullable();
            \$table->string('address')->nullable();
            \$table->string('city')->nullable();
            \$table->string('state')->nullable();
            \$table->string('postcode', 10)->nullable();
            \$table->string('pause_reason')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->enum('queue_status', ['open', 'paused', 'closed'])->default('closed');
            \$table->string('queue_prefix', 5)->default('Q');
            \$table->integer('current_number')->default(0);
            \$table->integer('daily_limit')->default(100);
            \$table->integer('entries_today')->default(0);
            \$table->integer('notify_turns_before')->default(3);
            \$table->timestamp('last_reset_at')->nullable();
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_subscriptions_table') !== false) {
        $schema = "            \$table->id();
            \$table->foreignId('business_id')->constrained()->cascadeOnDelete();
            \$table->enum('type', ['daily', 'monthly'])->default('daily');
            \$table->string('status')->default('active');
            \$table->timestamp('starts_at')->nullable();
            \$table->timestamp('expires_at')->nullable();
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_payments_table') !== false) {
        $schema = "            \$table->id();
            \$table->foreignId('business_id')->constrained()->cascadeOnDelete();
            \$table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            \$table->decimal('amount', 8, 2);
            \$table->string('currency', 3)->default('MYR');
            \$table->string('method')->default('fpx');
            \$table->string('status')->default('pending');
            \$table->string('reference')->nullable();
            \$table->timestamp('paid_at')->nullable();
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_counters_table') !== false) {
        $schema = "            \$table->id();
            \$table->foreignId('business_id')->constrained()->cascadeOnDelete();
            \$table->string('name');
            \$table->boolean('is_active')->default(true);
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_queue_entries_table') !== false) {
        $schema = "            \$table->id();
            \$table->foreignId('business_id')->constrained()->cascadeOnDelete();
            \$table->string('wa_id')->nullable();
            \$table->string('cancel_token')->nullable();
            \$table->integer('ticket_number');
            \$table->string('ticket_code');
            \$table->enum('status', ['waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled'])->default('waiting');
            \$table->string('source')->default('whatsapp');
            \$table->integer('position')->default(0);
            \$table->foreignId('counter_id')->nullable()->constrained()->nullOnDelete();
            \$table->timestamp('called_at')->nullable();
            \$table->timestamp('served_at')->nullable();
            \$table->timestamp('completed_at')->nullable();
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_qr_codes_table') !== false) {
        $schema = "            \$table->id();
            \$table->foreignId('business_id')->constrained()->cascadeOnDelete();
            \$table->string('label')->nullable();
            \$table->text('url');
            \$table->string('image_path')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_whatsapp_messages_table') !== false) {
        $schema = "            \$table->id();
            \$table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            \$table->foreignId('queue_entry_id')->nullable()->constrained()->nullOnDelete();
            \$table->string('wa_id');
            \$table->enum('direction', ['inbound', 'outbound']);
            \$table->string('template')->nullable();
            \$table->text('body');
            \$table->string('message_id')->nullable();
            \$table->string('status')->default('sent');
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_loyalty_rewards_table') !== false) {
        $schema = "            \$table->id();
            \$table->foreignId('business_id')->constrained()->cascadeOnDelete();
            \$table->integer('required_visits')->default(7);
            \$table->string('reward_type')->default('discount_code');
            \$table->string('reward_value');
            \$table->boolean('is_active')->default(true);
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_loyalty_visits_table') !== false) {
        $schema = "            \$table->id();
            \$table->foreignId('business_id')->constrained()->cascadeOnDelete();
            \$table->string('wa_id');
            \$table->foreignId('queue_entry_id')->nullable()->constrained()->nullOnDelete();
            \$table->integer('visit_number');
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_customer_feedback_table') !== false) {
        $schema = "            \$table->id();
            \$table->foreignId('business_id')->constrained()->cascadeOnDelete();
            \$table->foreignId('queue_entry_id')->nullable()->constrained()->nullOnDelete();
            \$table->string('wa_id');
            \$table->integer('rating');
            \$table->text('comment')->nullable();
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_invitations_table') !== false) {
        $schema = "            \$table->id();
            \$table->string('email');
            \$table->enum('role', ['platform_staff', 'business_staff']);
            \$table->unsignedBigInteger('business_id')->nullable();
            \$table->string('token', 64)->unique();
            \$table->unsignedBigInteger('invited_by');
            \$table->timestamp('expires_at');
            \$table->timestamp('accepted_at')->nullable();
            \$table->timestamps();";
        $content = preg_replace('/(\$table->id\(\);).*?(\$table->timestamps\(\);)/s', $schema, $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'add_saas_fields_to_users_table') !== false) {
        $up = "        Schema::table('users', function (Blueprint \$table) {
            \$table->unsignedBigInteger('business_id')->nullable();
            \$table->enum('role', ['superadmin', 'platform_staff', 'business_owner', 'business_staff'])->default('business_owner');
            \$table->string('phone', 20)->nullable();
            \$table->string('address')->nullable();
            \$table->string('city', 100)->nullable();
            \$table->string('state', 100)->nullable();
            \$table->string('postcode', 10)->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->boolean('profile_completed')->default(false);
        });";
        $down = "        Schema::table('users', function (Blueprint \$table) {
            \$table->dropColumn(['business_id', 'role', 'phone', 'address', 'city', 'state', 'postcode', 'is_active', 'profile_completed']);
        });";
        $content = preg_replace('/public function up\(\): void\s*\{\s*\}/s', "public function up(): void\n    {\n$up\n    }", $content);
        $content = preg_replace('/public function down\(\): void\s*\{\s*\}/s', "public function down(): void\n    {\n$down\n    }", $content);
        file_put_contents($file, $content);
    }
}
echo 'Migrations updated';

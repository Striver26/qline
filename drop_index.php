<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\Schema::table('customer_feedback', function($table) {
    $table->dropUnique('unique_feedback_per_ticket');
});
echo "Done";

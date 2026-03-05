<?php

// Run this file: php update_leave_policy_content.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Essentials\Entities\EssentialsPolicy;
use Modules\Essentials\Entities\PolicyTemplates;

echo "Fetching new Leave Policy content from PolicyTemplates...\n";
$newContent = PolicyTemplates::getTemplate('leave_policy');

echo "Updating leave policy records...\n";
$updated = EssentialsPolicy::where('policy_type', 'leave_policy')
    ->update(['content' => $newContent]);

echo "✓ Successfully updated {$updated} leave policy records with new content!\n";
echo "\nNow:\n";
echo "1. Clear browser cache (Ctrl + Shift + Delete)\n";
echo "2. Reload policy page (Ctrl + F5)\n";
echo "3. Download PDF again - new content will appear\n";

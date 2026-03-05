<?php
/**
 * Artisan Command to update existing company policy content
 * 
 * Usage: php artisan tinker
 * Then paste this code
 */

use Modules\Essentials\Entities\EssentialsPolicy;
use Modules\Essentials\Entities\PolicyTemplates;

// Get the new content from PolicyTemplates
$newContent = PolicyTemplates::getTemplate('company_policy');

// Update all existing company_policy records
$updated = EssentialsPolicy::where('policy_type', 'company_policy')
    ->update(['content' => $newContent]);

echo "Updated {$updated} company policy records with new content.\n";

// Or if you want to delete and let users re-sign:
// $deleted = EssentialsPolicy::where('policy_type', 'company_policy')->delete();
// echo "Deleted {$deleted} company policy records. Users will need to sign again.\n";

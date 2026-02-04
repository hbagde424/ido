<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ensure media table has model_media_type column for user documents
        if (Schema::hasTable('media')) {
            if (!Schema::hasColumn('media', 'model_media_type')) {
                Schema::table('media', function (Blueprint $table) {
                    $table->string('model_media_type')->nullable()->after('model_type');
                });
            }
            
            // Add index for better query performance when filtering by model_media_type
            Schema::table('media', function (Blueprint $table) {
                if (!$this->hasIndex('media', 'media_model_media_type_index')) {
                    $table->index('model_media_type', 'media_model_media_type_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Note: We don't drop the column as it might be used by other features
        // If you need to rollback, you can manually drop the index
        if (Schema::hasTable('media')) {
            Schema::table('media', function (Blueprint $table) {
                if ($this->hasIndex('media', 'media_model_media_type_index')) {
                    $table->dropIndex('media_model_media_type_index');
                }
            });
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function hasIndex($table, $indexName)
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        $result = $connection->select(
            "SELECT COUNT(*) as count 
             FROM information_schema.statistics 
             WHERE table_schema = ? 
             AND table_name = ? 
             AND index_name = ?",
            [$databaseName, $table, $indexName]
        );
        
        return $result[0]->count > 0;
    }
};

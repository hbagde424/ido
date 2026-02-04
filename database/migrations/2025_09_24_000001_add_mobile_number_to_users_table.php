<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobileNumberToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('users', 'mobile_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('mobile_number')->nullable()->after('email');
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
        if (Schema::hasColumn('users', 'mobile_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('mobile_number');
            });
        }
    }
}

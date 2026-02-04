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
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('remark');
            $table->date('end_date')->nullable()->after('start_date');
            $table->unsignedInteger('user_id')->nullable()->after('end_date');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['start_date', 'end_date', 'user_id']);
        });
    }
};

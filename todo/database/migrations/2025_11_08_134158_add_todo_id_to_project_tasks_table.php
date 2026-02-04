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
            $table->unsignedInteger('todo_id')->nullable()->after('project_checklist_id');
            $table->foreign('todo_id')->references('id')->on('essentials_to_dos')->onDelete('cascade');
            $table->unique('todo_id'); // Ensure one-to-one relationship
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
            $table->dropForeign(['todo_id']);
            $table->dropUnique(['todo_id']);
            $table->dropColumn('todo_id');
        });
    }
};

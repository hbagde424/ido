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
        Schema::table('essentials_to_dos', function (Blueprint $table) {
            $table->unsignedInteger('project_checklist_id')->nullable()->after('business_id');
            $table->foreign('project_checklist_id')->references('id')->on('project_checklists')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('essentials_to_dos', function (Blueprint $table) {
            $table->dropForeign(['project_checklist_id']);
            $table->dropColumn('project_checklist_id');
        });
    }
};

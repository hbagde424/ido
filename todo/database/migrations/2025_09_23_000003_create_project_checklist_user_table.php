<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectChecklistUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('project_checklist_user')) {
            Schema::create('project_checklist_user', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('project_checklist_id');
                $table->unsignedInteger('user_id');
                $table->timestamps();

                $table->foreign('project_checklist_id')->references('id')->on('project_checklists')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['project_checklist_id', 'user_id']);
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
        Schema::dropIfExists('project_checklist_user');
    }
}

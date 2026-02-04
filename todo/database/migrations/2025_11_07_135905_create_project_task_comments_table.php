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
        Schema::create('project_task_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_task_id');
            $table->unsignedInteger('user_id');
            $table->text('comment');
            $table->string('document_path')->nullable();
            $table->string('document_name')->nullable();
            $table->timestamps();
            
            $table->foreign('project_task_id')->references('id')->on('project_tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_task_comments');
    }
};

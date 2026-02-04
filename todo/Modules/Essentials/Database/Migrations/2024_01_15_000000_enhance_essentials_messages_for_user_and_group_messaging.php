<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceEssentialsMessagesForUserAndGroupMessaging extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add new columns to existing essentials_messages table
        Schema::table('essentials_messages', function (Blueprint $table) {
            $table->enum('message_type', ['location', 'user', 'group'])->default('location')->after('location_id');
            $table->integer('recipient_user_id')->nullable()->after('message_type');
            $table->integer('group_id')->nullable()->after('recipient_user_id');
        });

        // Create essentials_message_groups table
        Schema::create('essentials_message_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id');
            $table->string('group_name');
            $table->text('group_description')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });

        // Create essentials_message_group_members table
        Schema::create('essentials_message_group_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->integer('user_id');
            $table->integer('added_by');
            $table->timestamps();
            
            $table->unique(['group_id', 'user_id']);
        });

        // Create essentials_message_recipients table for tracking message delivery
        Schema::create('essentials_message_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('message_id');
            $table->integer('user_id');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->unique(['message_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('essentials_message_recipients');
        Schema::dropIfExists('essentials_message_group_members');
        Schema::dropIfExists('essentials_message_groups');
        
        Schema::table('essentials_messages', function (Blueprint $table) {
            $table->dropColumn(['message_type', 'recipient_user_id', 'group_id']);
        });
    }
}

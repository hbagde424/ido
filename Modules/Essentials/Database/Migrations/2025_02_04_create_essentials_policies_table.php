<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEssentialsPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essentials_policies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->index();
            $table->integer('user_id')->index();
            $table->enum('policy_type', ['company_policy', 'hr_policy', 'leave_policy', 'posh_policy', 'nda_policy']);
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('signature_photo')->nullable();
            $table->date('signed_date')->nullable();
            $table->enum('status', ['pending', 'signed', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('essentials_policies');
    }
}

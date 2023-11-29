<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsJobHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sms_job_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maker_id')->nullable(false);
            $table->foreignId('event_id')->nullable(false);
            $table->foreignId('job_id')->nullable(false);
            $table->string('status', 255)->nullable(false);
            $table->string('sending_type', 255);
            $table->dateTime('sending_date');
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
        Schema::dropIfExists('tbl_sms_job_history');
    }
}

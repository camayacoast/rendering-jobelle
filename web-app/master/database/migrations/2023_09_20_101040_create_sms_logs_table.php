<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maker_id');
            $table->foreignId('event_id');
            $table->integer('message_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user')->nullable();
            $table->integer('account_id')->nullable();
            $table->string('account')->nullable();
            $table->string('recipient')->nullable();
            $table->text('message')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('network')->nullable();
            $table->string('type')->nullable();
            $table->string('sending_type')->nullable();
            $table->string('source')->nullable();
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
        Schema::dropIfExists('tbl_sms_logs');
    }
}

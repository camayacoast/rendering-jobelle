<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInTblSmsJobHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_sms_job_history', function (Blueprint $table) {
            $table->string('event_name', 255)->nullable(false)->after('sending_date');
            $table->json('recepients')->after('event_name');
            $table->text('event_description')->nullable()->after('recepients');
            $table->text('message')->nullable(false)->after('event_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_sms_job_history', function (Blueprint $table) {
            $table->dropColumn(['event_name','recepients','event_description','message']);
        });
    }
}

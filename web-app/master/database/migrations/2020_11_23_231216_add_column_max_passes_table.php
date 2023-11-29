<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnMaxPassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('camaya_booking_db')->table('passes', function (Blueprint $table) {
            //
            $table->unsignedInteger('max')->nullable()->after('count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('camaya_booking_db')->table('passes', function (Blueprint $table) {
            //
            $table->dropColumn('max');
        });
    }
}

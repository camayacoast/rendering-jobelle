<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("camaya_booking_db")->create('meals', function (Blueprint $table) {
            $table->id();
            $table->string("name",45);
            $table->string("type",45);
            $table->date('available_from');
            $table->date( 'available_to');
            $table->text("description");
            $table->integer("status");
            $table->integer("deleted_by")->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('meals');
    }
}

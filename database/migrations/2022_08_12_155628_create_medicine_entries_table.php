<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicineEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicine_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('expiration_date');
            $table->double('quantity', 6, 2);
            $table->boolean('status');
            $table->unsignedBigInteger('medicine_id')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('medicine_id')->references('id')->on('medicines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicine_entries');
    }
}

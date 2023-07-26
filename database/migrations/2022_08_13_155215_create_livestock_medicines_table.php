<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLivestockMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('livestock_medicines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->double('dose', 6, 2);
            $table->unsignedBigInteger('livestock_id')->unsigned()->index();
            $table->unsignedBigInteger('medicine_entry_id')->unsigned()->index();
            $table->unsignedBigInteger('user_id')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('livestock_id')->references('id')->on('livestocks');
            $table->foreign('medicine_entry_id')->references('id')->on('medicine_entries');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('livestock_medicines');
    }
}

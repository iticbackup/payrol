<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewDataPengerjaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_data_pengerjaan', function (Blueprint $table) {
            // $table->id();
            $table->integer('id')->primary();
            $table->string('kode_pengerjaan');
            $table->date('date');
            $table->string('tanggal');
            $table->string('akhir_bulan',2);
            $table->string('status',2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_data_pengerjaan');
    }
}

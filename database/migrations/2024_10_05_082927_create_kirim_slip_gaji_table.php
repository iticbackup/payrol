<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKirimSlipGajiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kirim_slip_gaji', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengerjaan');
            $table->string('kode_payrol');
            $table->integer('pengerjaan_id');
            $table->string('nik');
            $table->string('nama_karyawan');
            $table->string('nominal_gaji');
            $table->string('status',50);
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
        Schema::dropIfExists('kirim_slip_gaji');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengerjaanHarianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengerjaan_harian', function (Blueprint $table) {
            // $table->id();
            $table->integer('id')->primary();
            $table->string('kode_pengerjaan');
            $table->string('kode_payrol');
            $table->integer('operator_harian_karyawan_id')->unsigned();
            $table->string('upah_dasar')->nullable();
            $table->string('upah_dasar_weekly')->nullable();
            $table->string('hari_kerja')->nullable();
            $table->string('hasil_kerja')->nullable();
            $table->string('tunjangan_kerja')->nullable();
            $table->string('tunjangan_kehadiran')->nullable();
            $table->string('plus_1')->nullable();
            $table->string('plus_2')->nullable();
            $table->string('plus_3')->nullable();
            $table->string('minus_1')->nullable();
            $table->string('minus_2')->nullable();
            $table->string('minus_3')->nullable();
            $table->string('uang_makan')->nullable();
            $table->string('lembur')->nullable();
            $table->string('jht')->nullable();
            $table->string('bpjs_kesehatan')->nullable();
            $table->string('pensiun')->nullable();
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
        Schema::dropIfExists('pengerjaan_harian');
    }
}

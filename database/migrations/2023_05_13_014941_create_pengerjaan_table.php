<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengerjaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengerjaan', function (Blueprint $table) {
            // $table->id();
            $table->integer('id')->primary();
            $table->string('kode_pengerjaan');
            $table->string('kode_payrol');
            $table->integer('operator_karyawan_id')->unsigned();
            $table->date('tanggal_pengerjaan');
            // $table->text('hasil_pengerjaan');
            $table->string('hasil_kerja_1')->nullable();
            $table->string('hasil_kerja_2')->nullable();
            $table->string('hasil_kerja_3')->nullable();
            $table->string('hasil_kerja_4')->nullable();
            $table->string('hasil_kerja_5')->nullable();
            $table->string('total_jam_kerja_1')->nullable();
            $table->string('total_jam_kerja_2')->nullable();
            $table->string('total_jam_kerja_3')->nullable();
            $table->string('total_jam_kerja_4')->nullable();
            $table->string('total_jam_kerja_5')->nullable();
            $table->string('uang_lembur')->nullable();
            $table->string('lembur')->nullable();
            // $table->string('upah_dasar')->nullable();
            // $table->string('tunjangan_kerja')->nullable();
            // $table->string('tunjangan_kehadiran')->nullable();
            // $table->string('uang_makan')->nullable();
            // $table->string('plus_1')->nullable();
            // $table->string('plus_2')->nullable();
            // $table->string('plus_3')->nullable();
            // $table->string('minus_1')->nullable();
            // $table->string('minus_2')->nullable();
            // $table->string('minus_3')->nullable();
            // $table->string('jht')->nullable();
            // $table->string('bpjs_kesehatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pengerjaan_weekly', function (Blueprint $table) {
            // $table->id();
            $table->integer('id')->primary();
            $table->string('kode_pengerjaan');
            $table->string('kode_payrol');
            $table->integer('operator_karyawan_id')->unsigned();
            $table->string('upah_dasar')->nullable();
            $table->string('tunjangan_kerja')->nullable();
            $table->string('tunjangan_kehadiran')->nullable();
            $table->string('uang_makan')->nullable();
            $table->string('plus_1')->nullable();
            $table->string('plus_2')->nullable();
            $table->string('plus_3')->nullable();
            $table->string('minus_1')->nullable();
            $table->string('minus_2')->nullable();
            $table->string('minus_3')->nullable();
            $table->string('jht')->nullable();
            $table->string('bpjs_kesehatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Schema::create('pengerjaan', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('kode_pengerjaan');
        //     $table->string('kode_hasil_pengerjaan');
        //     // $table->integer('operator_karyawan_id')->unsigned();
        //     // $table->date('tanggal');
        //     $table->string('tanggal_pengerjaan');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // Schema::create('pengerjaan_detail', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('pengerjaan_id')->unsigned();
        //     $table->integer('operator_karyawan_id')->unsigned();
        //     $table->date('tanggal_pengerjaan');
        //     // $table->text('hasil_pengerjaan');
        //     $table->string('hasil_kerja_1')->nullable();
        //     $table->string('hasil_kerja_2')->nullable();
        //     $table->string('hasil_kerja_3')->nullable();
        //     $table->string('hasil_kerja_4')->nullable();
        //     $table->string('hasil_kerja_5')->nullable();
        //     $table->string('total_jam_kerja_1')->nullable();
        //     $table->string('total_jam_kerja_2')->nullable();
        //     $table->string('total_jam_kerja_3')->nullable();
        //     $table->string('total_jam_kerja_4')->nullable();
        //     $table->string('total_jam_kerja_5')->nullable();
        //     $table->string('upah_dasar')->nullable();
        //     $table->string('tunjangan_kerja')->nullable();
        //     $table->string('tunjangan_kehadiran')->nullable();
        //     $table->string('uang_makan')->nullable();
        //     $table->string('lembur')->nullable();
        //     $table->string('plus_1')->nullable();
        //     $table->string('plus_2')->nullable();
        //     $table->string('plus_3')->nullable();
        //     $table->string('minus_1')->nullable();
        //     $table->string('minus_2')->nullable();
        //     $table->string('minus_3')->nullable();
        //     $table->string('jht')->nullable();
        //     $table->string('bpjs_kesehatan')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengerjaan');
        Schema::dropIfExists('pengerjaan_weekly');
        // Schema::dropIfExists('pengerjaan_detail');
    }
}

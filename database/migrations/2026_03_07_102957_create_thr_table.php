<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thr', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('periode');
            $table->year('tahun');
            $table->date('cut_off');
            $table->enum('status',['Y','N','C'])->default('Y');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('thr_detail', function (Blueprint $table) {
            $table->id('id');
            $table->uuid('thr_id')->unique();
            $table->string('nik_karyawan');
            $table->string('nama_karyawan');
            $table->string('bagian');
            $table->string('status');
            $table->date('masuk_kerja');
            $table->date('masa_kerja');
            $table->date('cut_off_thr');
            $table->date('masa_kerja_thr');
            $table->text('gaji_bulan_terakhir');
            $table->string('minimal_umk');
            $table->string('percentase',50);
            $table->string('thr_diterima');
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
        Schema::dropIfExists('thr');
        Schema::dropIfExists('thr_detail');
    }
}

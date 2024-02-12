<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiodataKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biodata_karyawan', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('nik')->nullable();
            $table->string('nama')->nullable();
            $table->string('alamat')->nullable();
            $table->string('id_posisi')->nullable();
            $table->string('id_jabatan')->nullable();
            $table->string('satuan_kerja')->nullable();
            $table->string('rekening')->nullable();
            $table->string('credit')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('status_klg')->nullable();
            $table->string('npwp')->nullable();
            $table->string('pin')->nullable();
            $table->string('status_karyawan')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_kontrak')->nullable();
            $table->date('tanggal_resign')->nullable();
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
        Schema::dropIfExists('biodata_karyawan');
    }
}

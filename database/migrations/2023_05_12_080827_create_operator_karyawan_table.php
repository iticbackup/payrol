<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperatorKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator_karyawan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nik');
            // $table->string('nama_karyawan');
            // $table->integer('level_id')->unsigned();
            $table->integer('jenis_operator_id')->unsigned();
            $table->integer('jenis_operator_detail_id')->unsigned();
            $table->integer('jenis_operator_detail_pekerjaan_id')->unsigned();
            $table->string('tunjangan_kerja_id',2)->nullable();
            $table->string('jht',2);
            $table->string('bpjs',2);
            $table->string('training',2);
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
        Schema::dropIfExists('operator_karyawan');
    }
}

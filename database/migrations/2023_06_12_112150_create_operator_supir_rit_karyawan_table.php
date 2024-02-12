<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperatorSupirRitKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator_supir_rit_karyawan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nik');
            $table->integer('rit_posisi_id')->unsigned();
            $table->string('tunjangan_kerja_id',2)->nullable();
            $table->string('upah_dasar');
            $table->string('jht',2);
            $table->string('bpjs',2);
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
        Schema::dropIfExists('operator_supir_rit_karyawan');
    }
}

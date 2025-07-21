<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestingBoronganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testing_borongan', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('kode_pengerjaan');
            $table->string('kode_payrol');
            $table->integer('operator_karyawan_id')->unsigned();
            $table->date('tanggal_pengerjaan');
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
        Schema::dropIfExists('testing_borongan');
    }
}

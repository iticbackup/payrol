<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRitUmkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rit_umk', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('kategori_upah');
            $table->integer('rit_posisi_id')->unsigned();
            $table->integer('rit_kendaraan_id')->unsigned();
            $table->integer('rit_tujuan_id')->unsigned();
            $table->string('tarif');
            $table->string('tahun_aktif',5);
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
        Schema::dropIfExists('rit_umk');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoronganUmkStempelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borongan_umk_stempel', function (Blueprint $table) {
            $table->BigInteger('id')->primary();
            $table->string('jenis_produk')->nullable();
            $table->string('nominal_umk')->nullable();
            $table->string('tahun_aktif',5)->nullable();
            $table->string('status',2)->nullable();
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
        Schema::dropIfExists('borongan_umk_stempel');
    }
}

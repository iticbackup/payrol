<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjsJhtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjs_jht', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('keterangan');
            $table->string('nominal');
            $table->string('masa_kerja');
            $table->string('tahun',5);
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
        Schema::dropIfExists('bpjs_jht');
    }
}

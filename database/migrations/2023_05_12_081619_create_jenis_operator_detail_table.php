<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJenisOperatorDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jenis_operator_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('jenis_operator_id')->unsigned();
            $table->string('jenis_posisi');
            $table->string('status',2);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('jenis_operator_detail_pekerjaan', function (Blueprint $table) {
            $table->id();
            $table->integer('jenis_operator_detail_id')->unsigned();
            $table->string('jenis_posisi_pekerjaan');
            $table->string('link');
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
        Schema::dropIfExists('jenis_operator_detail');
        Schema::dropIfExists('jenis_operator_detail_pekerjaan');
    }
}

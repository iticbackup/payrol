<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterCaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_case', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('pouch_packing');
            $table->integer('pouch_bandrol');
            $table->integer('pouch_inner');
            $table->integer('pouch_outer');
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
        Schema::dropIfExists('master_case');
    }
}
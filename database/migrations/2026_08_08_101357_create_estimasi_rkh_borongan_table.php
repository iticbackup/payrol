<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstimasiRkhBoronganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimasi_rkh_borongan', function (Blueprint $table) {
            $table->id();
            $table->string('rkh',100);
            $table->date('work_date');
            $table->string('master_case',100);
            $table->string('pouch');
            $table->double('umk_packing');
            $table->double('price_packing')->nullable();
            $table->double('umk_bandrol');
            $table->double('price_bandrol')->nullable();
            $table->double('umk_inner');
            $table->double('price_inner')->nullable();
            $table->double('umk_outer');
            $table->double('price_outer')->nullable();
            $table->integer('etiket');
            $table->double('umk_etiket');
            $table->double('price_etiket')->nullable();
            $table->double('umk_export_packing');
            $table->double('price_export_packing')->nullable();
            $table->double('umk_export_kemas');
            $table->double('price_export_kemas')->nullable();
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
        Schema::dropIfExists('estimasi_rkh_borongan');
    }
}

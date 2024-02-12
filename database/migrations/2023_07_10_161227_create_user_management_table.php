<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_management', function (Blueprint $table) {
            // $table->id();
            $table->uuid('id')->primary();
            $table->integer('user_id');
            $table->string('c',2)->nullable();
            $table->string('r',2)->nullable();
            $table->string('u',2)->nullable();
            $table->string('d',2)->nullable();
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
        Schema::dropIfExists('user_management');
    }
}

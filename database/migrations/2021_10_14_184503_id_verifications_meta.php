<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IdVerificationsMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('id_verifications_meta', function (Blueprint $table) {
            $table->id();
            $table->integer('verify_id');
            $table->string('path');
            $table->integer('type')->default(0)->comment("0: goverment, 1: proof, 2: id card");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('id_verifications_meta');
    }
}

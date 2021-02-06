<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageMaskingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_maskings', function (Blueprint $table) {
            $table->unsignedBigInteger('masking_id');
            $table->unsignedBigInteger('message_id');
            $table->primary(['message_id', 'masking_id']);
            $table->foreign('masking_id')->references('id')->on('maskings')->onDelete('cascade');
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
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
        Schema::dropIfExists('message_maskings');
    }
}

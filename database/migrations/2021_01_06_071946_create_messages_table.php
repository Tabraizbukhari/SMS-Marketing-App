<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('masking_id');
            $table->unsignedBigInteger('contact_number');
            $table->text('message');
            $table->unsignedBigInteger('message_length');
            $table->double('price')->nullable();
            $table->enum('status', ['successfully','pending','not_sent']);
            $table->enum('type', ['single','campaign'])->default('single');
            $table->timestamp('send_date');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('masking_id')->references('id')->on('maskings')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}

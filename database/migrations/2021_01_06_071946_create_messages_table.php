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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('message_id')->nullable();
            $table->BigInteger('contact_number');
            $table->text('message');
            $table->unsignedBigInteger('message_length');
            $table->double('price')->nullable();
            $table->enum('status', ['successfully','pending','not_sent']);
            $table->enum('type', ['single','campaign'])->default('single');
            $table->enum('api_type', ['masking','code'])->default('masking');
            $table->enum('is_verified', [0,1])->default(0);
            $table->timestamp('send_date');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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

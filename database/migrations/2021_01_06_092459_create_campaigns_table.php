<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();           
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('name');
            $table->string('file_url');
            $table->string('file_name');
            $table->string('size');
            $table->timestamp('campaign_date')->nullable();
            $table->enum('status',['pending', 'canceled', 'completed', 'failed']);
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
        Schema::dropIfExists('campaigns');
    }
}

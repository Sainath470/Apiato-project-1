<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContainerBlogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('container_blogs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('admin_id');
            $table->string('hotelName');
            $table->string('name');
            $table->string('type');
            $table->integer('price');
            $table->float('rating');
            $table->string('place');
            $table->foreign('admin_id')->references('id')->on('container_admin')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->timestamps();
            //$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('container_blogs');
    }
}

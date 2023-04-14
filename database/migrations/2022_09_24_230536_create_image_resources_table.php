<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_resources', function (Blueprint $table) {
            $table->id();
            $table->string("filename");
            $table->string("image_id");
            $table->text("path");
            $table->text("url");
            $table->double("size");
            $table->morphs("imageable");
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
        Schema::dropIfExists('image_resources');
    }
}

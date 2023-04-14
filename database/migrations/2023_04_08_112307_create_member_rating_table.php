<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_rating', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("member_id");
            $table->bigInteger("member_id_rating");
            $table->bigInteger("transaction_id");
            $table->bigInteger("rating");
            $table->string("description");
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
        Schema::dropIfExists('member_rating');
    }
}

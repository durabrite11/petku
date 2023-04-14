<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_locations', function (Blueprint $table) {
            $table->id();
            $table->integer("member_id");
            $table->string("address");
            $table->string("latitude");
            $table->string("longitude");
            $table->string("description");
            $table->char("active")->default(0);
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
        Schema::dropIfExists('member_locations');
    }
}

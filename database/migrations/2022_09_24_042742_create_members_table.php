<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('address')->nullable();
            $table->string('type')->nullable()->default("Member");
            $table->string('fcm_token')->nullable();
            $table->string('password');
            $table->string('question');
            $table->string('answer');
            $table->char('status')->default("1");
            $table->char('aktif')->default("0");
            $table->time('hour_open')->default('08:00')->nullable();
            $table->time('hour_close')->default('17:00')->nullable();
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
        Schema::dropIfExists('members');
    }
}

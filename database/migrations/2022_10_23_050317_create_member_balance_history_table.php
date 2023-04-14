<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberBalanceHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_balance_history', function (Blueprint $table) {
            $table->id();
            $table->integer("member_id");
            $table->double("balance_before");
            $table->double("balance_in");
            $table->double("balance_out");
            $table->double("balance_after");
            $table->string("module");
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
        Schema::dropIfExists('member_balance_history');
    }
}

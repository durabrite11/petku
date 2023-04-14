<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topup', function (Blueprint $table) {
            $table->id();
            $table->integer("member_id");
            $table->integer("bank_id");
            $table->String("code");
            $table->String("bank");
            $table->String("bank_account_name");
            $table->String("bank_account_number");
            $table->double("nominal");
            $table->double("charge");
            $table->double("random_digit")->default(0);
            $table->double("total_transfer");
            $table->string("status")->default("PENDING");
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
        Schema::dropIfExists('topup');
    }
}

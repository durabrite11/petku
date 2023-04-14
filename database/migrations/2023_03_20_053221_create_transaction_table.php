<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->integer("member_id");
            $table->integer("member_id_groomer");
            $table->string("code");
            $table->string("member_name");
            $table->text("member_address");
            $table->string("member_latitude");
            $table->string("member_longitude");
            $table->string("groomer_name");
            $table->text("groomer_address");
            $table->string("groomer_latitude");
            $table->string("groomer_longitude");
            $table->double("distance");
            $table->double("price_per_km");
            $table->double("service_price");
            $table->double("delivery_price");
            $table->double("total_price");
            $table->string("status_transaction")->default("PENDING");
            $table->string("type")->default("OUT");
            $table->dateTime("schedule");
            $table->dateTime("transaction_date");
            $table->dateTime("expired_date");
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
        Schema::dropIfExists('transaction');
    }
}

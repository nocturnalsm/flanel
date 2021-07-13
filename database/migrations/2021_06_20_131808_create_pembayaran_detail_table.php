<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaranDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembayaran_detail', function (Blueprint $table) {
            $table->bigIncrements("ID");
            $table->unsignedInteger("ID_HEADER")->index();
            $table->unsignedInteger("JOB_ORDER_ID")->index();
            $table->unsignedInteger("KODE_TRANSAKSI")->index();
            $table->decimal("NOMINAL", 13, 2)->default(0);
            $table->char("DK", 1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembayaran_detail');
    }
}

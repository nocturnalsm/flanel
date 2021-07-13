<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOrderDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelian_detail', function (Blueprint $table) {
            $table->bigIncrements("ID");
            $table->unsignedInteger("ID_HEADER")->index();
            $table->string("NAMA_BARANG")->nullable();
            $table->unsignedInteger("SATUAN_ID")->nullable();
            $table->decimal("QTY",13,2)->default(0);
            $table->decimal("NOMINAL", 13,2)->default(0);
        });
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->bigIncrements("ID");
            $table->unsignedInteger("ID_HEADER")->index();
            $table->unsignedInteger("JENISDOKUMEN_ID")->index();
            $table->string("NO_INV")->nullable();
            $table->date("TGL_INV")->nullable();
            $table->unsignedInteger("PEMBELI_ID")->nullable();
            $table->decimal("QTY",13,2)->default(0);
            $table->decimal("HARGA", 13,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_order_detail');
    }
}

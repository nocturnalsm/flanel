<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_order', function (Blueprint $table) {
            $table->bigIncrements("ID");
            $table->string("JOB_ORDER")->index();
            $table->date("TGL_JOB")->nullable();
            $table->unsignedInteger("JENIS_DOK")->index();
            $table->string("TOTAL_MODAL", 6)->nullable();
            $table->date("TGL_TIBA")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_order');
    }
}

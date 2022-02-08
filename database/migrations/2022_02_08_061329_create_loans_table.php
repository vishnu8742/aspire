<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_amount');
            $table->string('final_amount');
            $table->string('loan_type')->default('personal');
            $table->string('loan_term')->default('4');
            $table->string('loan_purpose')->nullable();
            $table->string('interest')->default(0);
            $table->string('status')->default('pending');
            $table->string('user_id');
            $table->integer('repay_days')->default(7);
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
        Schema::dropIfExists('loans');
    }
}

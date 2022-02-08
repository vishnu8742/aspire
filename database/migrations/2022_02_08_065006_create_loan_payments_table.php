<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_id');
            $table->integer('on_time')->default(1);
            $table->string('amount');
            $table->string('payment_method')->nullable();
            $table->string('payment_date');
            $table->string('late_pay_charge')->default(0);
            $table->string('status')->default('pending');
            $table->string('payment_done_date')->nullable();
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
        Schema::dropIfExists('loan_payments');
    }
}

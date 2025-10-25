<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaypalFieldsToOrdenes extends Migration
{
    public function up()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->string('paypal_order_id')->nullable();
            $table->string('estado')->default('pendiente');
        });
    }

    public function down()
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropColumn('paypal_order_id');
            $table->dropColumn('estado');
        });
    }
}
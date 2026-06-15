<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('g_number')->nullable()->index();
            $table->dateTime('date')->nullable();
            $table->date('last_change_date')->nullable();

            $table->string('supplier_article')->nullable();
            $table->string('tech_size')->nullable();
            $table->string('barcode')->nullable()->index();

            $table->decimal('total_price', 12, 2)->nullable();
            $table->integer('discount_percent')->nullable();

            $table->string('warehouse_name')->nullable()->index();
            $table->string('oblast')->nullable();

            $table->bigInteger('income_id')->nullable();
            $table->bigInteger('odid')->nullable();

            $table->bigInteger('nm_id')->nullable()->index();
            $table->string('subject')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();

            $table->boolean('is_cancel')->nullable();
            $table->dateTime('cancel_dt')->nullable();

            $table->string('external_hash', 32)->unique();
            $table->json('raw_data');
            $table->timestamp('imported_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

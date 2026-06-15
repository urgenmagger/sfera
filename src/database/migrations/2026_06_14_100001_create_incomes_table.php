<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('income_id')->nullable()->index();
            $table->string('number')->nullable();
            $table->date('date')->nullable();
            $table->date('last_change_date')->nullable();

            $table->string('supplier_article')->nullable();
            $table->string('tech_size')->nullable();
            $table->string('barcode')->nullable()->index();

            $table->integer('quantity')->nullable();
            $table->decimal('total_price', 12, 2)->nullable();
            $table->date('date_close')->nullable();

            $table->string('warehouse_name')->nullable()->index();
            $table->bigInteger('nm_id')->nullable()->index();
            $table->string('status')->nullable();

            $table->string('external_hash', 32)->unique();
            $table->json('raw_data');
            $table->timestamp('imported_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}

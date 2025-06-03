<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('g_number');
            $table->dateTime('date');
            $table->dateTime('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->bigInteger('barcode');
            $table->decimal('total_price', 15, 2);
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->boolean('is_supply');
            $table->boolean('is_realization');
            $table->decimal('promo_code_discount', 15, 2)->nullable();
            $table->string('warehouse_name');
            $table->string('country_name');
            $table->string('oblast_okrug_name');
            $table->string('region_name');
            $table->bigInteger('income_id');
            $table->string('sale_id');
            $table->string('odid')->nullable();
            $table->decimal('spp', 5, 2);
            $table->decimal('for_pay', 15, 2);
            $table->decimal('finished_price', 15, 2);
            $table->decimal('price_with_disc', 15, 2);
            $table->bigInteger('nm_id');
            $table->string('subject');
            $table->string('category');
            $table->string('brand');
            $table->boolean('is_storno')->nullable();
            $table->timestamps();
            $table->index('g_number');
            $table->index('income_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

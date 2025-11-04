<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique(); // Auto-generated unique barcode
            $table->string('product_name')->unique();
            $table->string('category_id'); // Can be a foreign key or just category name
            $table->string('brand')->nullable();
            $table->string('unit'); // e.g. Pack, Bottle
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

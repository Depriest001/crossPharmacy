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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Seller & cashier
            $table->foreignId('seller_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('cashier_id')->nullable()->constrained('staff')->onDelete('cascade');

            // Optional branch link
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();

            // Totals
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);

            // Payment
            $table->string('payment_method')->nullable(); // cashier will set this

            // POS Flow status
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');

            $table->timestamps();
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

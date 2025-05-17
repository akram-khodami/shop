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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shipping_method_id')->constrained()->onDelete('cascade');
            $table->decimal('total_price', 10, 2);
            $table->decimal('shipping_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
//            $table->decimal('tax_amount', 10, 2);//???
            $table->string('status')->default('pending')->comment('pending, processing, paid, shipped,completed,cancelled, etc.');
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('shipping_address_id')->constrained('addresses')->onDelete('restrict');
            $table->foreignId('billing_address_id')->constrained('addresses')->onDelete('restrict');
            $table->string('payment_status'); // pending, paid, failed, refunded
            $table->string('shipping_status'); // pending, shipped, delivered
            $table->string('payment_method');
            $table->text('notes')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

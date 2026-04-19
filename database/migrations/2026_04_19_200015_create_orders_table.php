<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email');
            $table->string('phone', 32)->nullable();
            $table->string('status', 32)->default('pending'); // pending|paid|packed|shipped|delivered|cancelled|refunded
            $table->decimal('subtotal_inr', 12, 2)->default(0);
            $table->decimal('shipping_inr', 12, 2)->default(0);
            $table->decimal('discount_inr', 12, 2)->default(0);
            $table->decimal('gst_inr', 12, 2)->default(0);
            $table->decimal('total_inr', 12, 2)->default(0);
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('payment_gateway', 16)->nullable(); // razorpay|stripe|cod
            $table->string('coupon_code')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_gift')->default(false);
            $table->text('gift_message')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('packed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

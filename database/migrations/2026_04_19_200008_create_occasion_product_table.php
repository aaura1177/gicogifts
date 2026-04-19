<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('occasion_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('occasion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['occasion_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occasion_product');
    }
};

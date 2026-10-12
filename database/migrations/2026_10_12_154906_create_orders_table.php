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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('order_type', ['dine_in', 'takeaway']);
            $table->enum('payment_method', ['cod', 'online']);
            $table->unsignedInteger('total_harga');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired', 'cod_pending'])->default('pending');
            $table->enum('order_status', ['baru', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan'])->default('baru');
            $table->timestamps();
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

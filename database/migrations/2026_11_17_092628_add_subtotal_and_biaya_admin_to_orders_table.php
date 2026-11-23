<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('subtotal')->default(0)->after('total_harga');
            $table->unsignedInteger('biaya_admin')->default(0)->after('subtotal');
        });

        DB::table('orders')->update(['subtotal' => DB::raw('total_harga')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'biaya_admin']);
        });
    }
};

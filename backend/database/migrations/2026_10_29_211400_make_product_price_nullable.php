<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        // برای برگشت، قیمت محصولات بدون قیمت را صفر می‌کنیم.
        DB::table('products')
            ->whereNull('price')
            ->update(['price' => 0]);

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable(false)->change();
        });
    }
};


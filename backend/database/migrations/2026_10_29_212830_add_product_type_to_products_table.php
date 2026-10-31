
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
            $table->string('product_type')
                ->default('simple')
                ->after('name');
        });

        // محصولاتی که از قبل تنوع دارند،
        // به‌عنوان محصول تنوع‌دار مشخص می‌شوند.
        if (Schema::hasTable('product_variants')) {
            DB::table('products')
                ->whereIn(
                    'id',
                    DB::table('product_variants')
                        ->select('product_id')
                )
                ->update([
                    'product_type' => 'variant',
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'is_free')) {
                $table->boolean('is_free')->default(false)->after('line_total');
            }
            if (!Schema::hasColumn('order_items', 'promotion_id')) {
                $table->foreignId('promotion_id')->nullable()->after('is_free')->constrained('promotions')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'promotion_id')) {
                $table->dropForeign(['promotion_id']);
                $table->dropColumn('promotion_id');
            }
            if (Schema::hasColumn('order_items', 'is_free')) {
                $table->dropColumn('is_free');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('free_gift_promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('active');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->decimal('minimum_cart_amount', 10, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('free_gift_promotion_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('free_gift_promotion_id')
                ->constrained('free_gift_promotions', indexName: 'fgp_prod_promo_fk')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained('products', indexName: 'fgp_prod_product_fk')
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('free_gift_promotion_excluded_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('free_gift_promotion_id')
                ->constrained('free_gift_promotions', indexName: 'fgp_ex_cat_promo_fk')
                ->cascadeOnDelete();
            $table->foreignId('category_id')
                ->constrained('categories', indexName: 'fgp_ex_cat_cat_fk')
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_free_gift')) {
                $table->boolean('is_free_gift')->default(false)->after('is_active');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'is_free_gift')) {
                $table->boolean('is_free_gift')->default(false)->after('is_free');
            }
            if (!Schema::hasColumn('order_items', 'free_gift_promotion_id')) {
                $table->foreignId('free_gift_promotion_id')
                    ->nullable()
                    ->after('is_free_gift')
                    ->constrained('free_gift_promotions', indexName: 'order_items_fg_promo_fk')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'free_gift_promotion_id')) {
                $table->dropForeign('order_items_fg_promo_fk');
                $table->dropColumn('free_gift_promotion_id');
            }
            if (Schema::hasColumn('order_items', 'is_free_gift')) {
                $table->dropColumn('is_free_gift');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_free_gift')) {
                $table->dropColumn('is_free_gift');
            }
        });

        Schema::dropIfExists('free_gift_promotion_excluded_categories');
        Schema::dropIfExists('free_gift_promotion_products');
        Schema::dropIfExists('free_gift_promotions');
    }
};

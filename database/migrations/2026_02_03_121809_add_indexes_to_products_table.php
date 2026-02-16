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
        Schema::table('products', function (Blueprint $table) {
            // Single column indexes
            $table->index('name', 'idx_products_name');
            $table->index('slug', 'idx_products_slug');
            $table->index('brand_id', 'idx_products_brand');
            $table->index('added_by', 'idx_products_added_by');

            // Composite indexes for performance on filters
            $table->index(['status', 'published'], 'idx_products_status_published');
            $table->index(['category_id', 'sub_category_id'], 'idx_products_category_hierarchy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_name');
            $table->dropIndex('idx_products_slug');
            $table->dropIndex('idx_products_brand');
            $table->dropIndex('idx_products_added_by');
            $table->dropIndex('idx_products_status_published');
            $table->dropIndex('idx_products_category_hierarchy');
        });
    }
};

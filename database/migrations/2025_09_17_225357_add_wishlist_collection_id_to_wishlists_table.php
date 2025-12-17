<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->bigInteger('quantity')->nullable()->after('product_id');
            $table->unsignedBigInteger('wishlist_collection_id')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            //
        });
    }
};

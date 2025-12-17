<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {

            $table->unsignedBigInteger('governorate_id')->nullable()->after('address');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {

            $table->dropForeign(['governorate_id']);
            $table->dropColumn('governorate_id');
        });
    }
};

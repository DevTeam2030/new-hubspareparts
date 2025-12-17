<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGovernorateIdToSellersTable extends Migration
{

    public function up()
    {
        Schema::table('sellers', function (Blueprint $table) {

            $table->unsignedBigInteger('governorate_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('sellers', function (Blueprint $table) {

            $table->dropForeign(['governorate_id']);

            $table->dropColumn('governorate_id');
        });
    }
}

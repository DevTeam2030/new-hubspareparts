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
        Schema::create('wishlist_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('due_date');
            $table->string('priority');
            $table->string('notes')->nullable();
            $table->date('eng_approve')->nullable();
            $table->unsignedBigInteger('eng_approve_user_id')->nullable();
            $table->date('eng_proc')->nullable();
            $table->unsignedBigInteger('eng_proc_user_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('eng_approve_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('eng_proc_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('count_products')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_collections');
    }
};

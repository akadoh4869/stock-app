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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // アイテム名
            $table->foreignId('category_id')->constrained('inventory_categories')->onDelete('cascade'); // カテゴリ
            $table->integer('quantity')->default(1); // 数量
            $table->text('description')->nullable(); // 説明
            $table->date('purchase_date')->nullable(); // 購入日
            $table->date('expiration_date')->nullable(); // 消費期限
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null'); // 所有者
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};

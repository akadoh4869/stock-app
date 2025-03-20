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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 在庫管理スペース名
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('cascade'); // 個人の在庫管理者
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade'); // グループの在庫管理
            $table->timestamps();
    
            // 個人またはグループ、どちらか1つだけ指定する
            $table->unique(['owner_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

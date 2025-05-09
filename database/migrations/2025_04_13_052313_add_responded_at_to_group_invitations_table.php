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
        Schema::table('group_invitations', function (Blueprint $table) {
            $table->timestamp('responded_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_invitations', function (Blueprint $table) {
            $table->dropColumn('responded_at'); // ← これを追加！
        });
    }
};


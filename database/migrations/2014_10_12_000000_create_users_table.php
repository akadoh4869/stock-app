<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ユーザーID (BIGINT, AUTO_INCREMENT, PRIMARY KEY)
            $table->string('name');
            $table->string('user_name')->unique(); // ユーザーネーム (一意制約)
            $table->string('email')->unique(); // メールアドレス (一意制約)
            $table->timestamp('email_verified_at')->nullable(); // メール認証時刻 (null可)
            $table->string('password'); // パスワード
            $table->rememberToken(); // ログイン保持用トークン
            $table->boolean('subscription_status')->default(false); // サブスク加入ステータス (デフォルト: 無課金)
            $table->boolean('is_admin')->default(false); // 管理者フラグ (デフォルト: false)
            $table->string('avatar')->nullable(); // アバター画像 (NULL可)
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

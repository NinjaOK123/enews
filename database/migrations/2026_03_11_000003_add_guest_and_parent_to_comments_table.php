<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // user_id có thể null nếu là bình luận khách (Komento guest)
            $table->foreignId('user_id')->nullable()->change();

            // Dữ liệu khách (Komento: name, email khi chưa đăng nhập)
            $table->string('guest_name')->nullable()->after('user_id');
            $table->string('guest_email')->nullable()->after('guest_name');

            // Bình luận lồng nhau (Komento: parent_id)
            $table->unsignedBigInteger('parent_id')->nullable()->after('guest_email');
            $table->foreign('parent_id')->references('id')->on('comments')->nullOnDelete();

            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropColumn(['guest_name', 'guest_email', 'parent_id']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};

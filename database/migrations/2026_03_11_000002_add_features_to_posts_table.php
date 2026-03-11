<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Trang chủ nổi bật (Joomla: enews_content_frontpage / featured=1)
            $table->boolean('is_featured')->default(false)->after('view_count');
            // SEO (Joomla: metadesc, metakey)
            $table->string('meta_desc', 1024)->nullable()->after('is_featured');
            $table->string('meta_key', 1024)->nullable()->after('meta_desc');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'meta_desc', 'meta_key']);
        });
    }
};

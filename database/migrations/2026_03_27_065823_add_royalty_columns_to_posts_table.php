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
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('royalty_rate_id')->nullable()->after('view_count')->constrained('royalty_rates')->nullOnDelete();
            $table->integer('royalty_multiplier')->default(1)->after('royalty_rate_id')->comment('Hệ số / Chiết tính');
            $table->integer('image_count')->default(0)->after('royalty_multiplier')->comment('Số lượng ảnh tính nhuận bút (10k/ảnh)');
            $table->bigInteger('royalty_total')->default(0)->after('image_count')->comment('Thành tiền = (amount * multiplier) + (image_count * 10000)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['royalty_rate_id']);
            $table->dropColumn(['royalty_rate_id', 'royalty_multiplier', 'image_count', 'royalty_total']);
        });
    }
};

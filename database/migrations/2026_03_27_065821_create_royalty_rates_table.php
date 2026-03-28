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
        Schema::create('royalty_rates', function (Blueprint $table) {
            $table->id();
            $table->string('group_name'); // Nhóm thể loại (vd: 1 Tin, 2 Thông tin chuyên sâu)
            $table->string('name'); // Tên định mức (vd: Tin tự viết, Phỏng vấn)
            $table->string('unit'); // ĐV tính (vd: đồng/tin bài)
            $table->integer('amount'); // Số tiền
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('royalty_rates');
    }
};

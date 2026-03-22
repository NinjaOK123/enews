<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');                    // Tên cuộc thi / banner
            $table->string('image');                    // Đường dẫn ảnh (storage)
            $table->string('link')->nullable();         // URL khi click vào banner
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);       // Thứ tự hiển thị
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};

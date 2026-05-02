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
        Schema::table('contributor_requests', function (Blueprint $table) {
            $table->string('mssv')->nullable()->after('full_name');
            $table->string('class_name')->nullable()->after('mssv');
            $table->string('id_card')->nullable()->after('class_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contributor_requests', function (Blueprint $table) {
            $table->dropColumn(['mssv', 'class_name', 'id_card']);
        });
    }
};

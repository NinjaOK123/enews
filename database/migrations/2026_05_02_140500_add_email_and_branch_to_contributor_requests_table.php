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
            $table->string('email')->nullable()->after('full_name');
            $table->string('bank_branch')->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contributor_requests', function (Blueprint $table) {
            $table->dropColumn(['email', 'bank_branch']);
        });
    }
};

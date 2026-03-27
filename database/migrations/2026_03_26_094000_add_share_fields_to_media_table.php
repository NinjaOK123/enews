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
        Schema::table('media', function (Blueprint $table) {
            $table->string('shared_role')->nullable()->after('is_shared');
            $table->foreignId('shared_user_id')->nullable()->constrained('users')->nullOnDelete()->after('shared_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['shared_user_id']);
            $table->dropColumn(['shared_role', 'shared_user_id']);
        });
    }
};

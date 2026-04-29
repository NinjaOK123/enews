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
        Schema::create('plagiarism_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->nullable()->constrained('posts')->onDelete('cascade');
            $table->string('external_url')->nullable();
            $table->string('title')->nullable();
            $table->integer('total_fingerprints')->default(0);
            $table->timestamps();
        });

        Schema::create('plagiarism_fingerprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('plagiarism_documents')->onDelete('cascade');
            $table->unsignedBigInteger('hash_value')->index(); // The inverted index!
            $table->integer('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plagiarism_tables');
    }
};

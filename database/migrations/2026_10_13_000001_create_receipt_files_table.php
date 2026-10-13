<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Stores receipt images inside the database (RECEIPT_DISK=database) for
     * hosting platforms without a persistent filesystem, such as Vercel.
     */
    public function up(): void
    {
        Schema::create('receipt_files', function (Blueprint $table) {
            $table->id();
            $table->string('path')->unique(); // e.g. receipts/abc123.jpg
            $table->string('mime_type', 100);
            $table->longText('content'); // base64-encoded image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_files');
    }
};

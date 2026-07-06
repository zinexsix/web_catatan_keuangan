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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->tinyInteger('due_day'); // Day of month (1-31)
            $table->enum('recurrence', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->string('category')->nullable(); // e.g., Internet, Listrik
            $table->string('icon')->default('fa-file-invoice'); // FontAwesome icon
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};

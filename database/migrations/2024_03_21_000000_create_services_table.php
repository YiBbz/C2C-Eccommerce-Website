<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('category');
            $table->integer('delivery_time');
            $table->integer('revisions')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('category');
            $table->index('price');
            $table->index(['user_id', 'status']);
            $table->index(['category', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
}; 
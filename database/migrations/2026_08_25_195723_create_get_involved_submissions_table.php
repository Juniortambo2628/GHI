<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('get_involved_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->foreignId('initiative_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('new');
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('get_involved_submissions');
    }
};

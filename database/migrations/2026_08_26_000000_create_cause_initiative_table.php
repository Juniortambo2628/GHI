<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cause_initiative', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cause_id')->constrained()->cascadeOnDelete();
            $table->foreignId('initiative_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['cause_id', 'initiative_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cause_initiative');
    }
};

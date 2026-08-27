<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('get_involved_submissions', function (Blueprint $table) {
            $table->index('initiative_id');
        });
    }

    public function down(): void
    {
        Schema::table('get_involved_submissions', function (Blueprint $table) {
            $table->dropIndex(['initiative_id']);
        });
    }
};

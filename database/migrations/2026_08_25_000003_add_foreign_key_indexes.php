<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('initiatives', function (Blueprint $table) {
            if (!Schema::hasIndex('initiatives', ['cause_id'])) {
                $table->index('cause_id');
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasIndex('events', ['initiative_id'])) {
                $table->index('initiative_id');
            }
        });

        Schema::table('impact_activities', function (Blueprint $table) {
            if (!Schema::hasIndex('impact_activities', ['event_id'])) {
                $table->index('event_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('initiatives', function (Blueprint $table) {
            $table->dropIndex(['cause_id']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['initiative_id']);
        });

        Schema::table('impact_activities', function (Blueprint $table) {
            $table->dropIndex(['event_id']);
        });
    }
};

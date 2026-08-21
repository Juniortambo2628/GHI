<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add likes and comments columns to stories
        Schema::table('stories', function (Blueprint $table) {
            $table->integer('likes')->default(0)->after('image');
            $table->integer('comments')->default(0)->after('likes');
        });

        // Add status index to causes
        Schema::table('causes', function (Blueprint $table) {
            $table->index('status');
            $table->index('display_order');
        });

        // Add UNIQUE constraint to stories.slug
        Schema::table('stories', function (Blueprint $table) {
            $table->unique('slug');
        });

        // Add status index to newsletter_subscribers
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->index('status');
        });

        // Add composite index to contact_submissions
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn(['likes', 'comments']);
        });

        Schema::table('causes', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['display_order']);
        });
    }
};

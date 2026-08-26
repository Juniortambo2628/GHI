<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing 'icon' column to causes (defined in model + form requests but missing from DB)
        Schema::table('causes', function (Blueprint $table) {
            if (!Schema::hasColumn('causes', 'icon')) {
                $table->string('icon', 100)->nullable()->after('quote');
            }
        });

        // Add missing columns to contact_submissions
        Schema::table('contact_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_submissions', 'firstname')) {
                $table->string('firstname', 255)->nullable()->after('id');
            }
            if (!Schema::hasColumn('contact_submissions', 'lastname')) {
                $table->string('lastname', 255)->nullable()->after('firstname');
            }
            if (!Schema::hasColumn('contact_submissions', 'phone')) {
                $table->string('phone', 50)->nullable()->after('email');
            }
            if (!Schema::hasColumn('contact_submissions', 'subject')) {
                $table->string('subject', 255)->nullable()->after('phone');
            }
        });

        // Add missing columns to impact_activities
        Schema::table('impact_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('impact_activities', 'metric_type')) {
                $table->string('metric_type', 50)->nullable()->after('display_order');
            }
            if (!Schema::hasColumn('impact_activities', 'metric_value')) {
                $table->decimal('metric_value', 10, 2)->nullable()->after('metric_type');
            }
            if (!Schema::hasColumn('impact_activities', 'activity_date')) {
                $table->date('activity_date')->nullable()->after('metric_value');
            }
            if (!Schema::hasColumn('impact_activities', 'location')) {
                $table->string('location', 255)->nullable()->after('activity_date');
            }
            if (!Schema::hasColumn('impact_activities', 'featured')) {
                $table->boolean('featured')->default(false)->after('location');
            }
        });

        // Add missing columns to stories
        Schema::table('stories', function (Blueprint $table) {
            if (!Schema::hasColumn('stories', 'author')) {
                $table->string('author', 255)->nullable()->after('content');
            }
            if (!Schema::hasColumn('stories', 'featured_image')) {
                $table->string('featured_image', 255)->nullable()->after('author');
            }
        });

        // Add missing indexes
        Schema::table('contact_submissions', function (Blueprint $table) {
            if (!Schema::hasIndex('contact_submissions', 'idx_contact_status_created')) {
                $table->index(['status', 'created_at'], 'idx_contact_status_created');
            }
        });

        // Fix status enums to include 'archived' for MySQL-based deployments.
        // SQLite does not support ALTER TABLE ... MODIFY, so we skip this for local development.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE causes MODIFY status VARCHAR(20) NOT NULL DEFAULT 'draft'");
            DB::statement("ALTER TABLE initiatives MODIFY status VARCHAR(20) NOT NULL DEFAULT 'draft'");
            DB::statement("ALTER TABLE events MODIFY status VARCHAR(20) NOT NULL DEFAULT 'draft'");
            DB::statement("ALTER TABLE impact_activities MODIFY status VARCHAR(20) NOT NULL DEFAULT 'draft'");
            DB::statement("ALTER TABLE stories MODIFY status VARCHAR(20) NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropIndex('idx_contact_status_created');
            $table->dropColumn(['firstname', 'lastname', 'phone', 'subject']);
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn(['author', 'featured_image']);
        });

        Schema::table('impact_activities', function (Blueprint $table) {
            $table->dropColumn(['metric_type', 'metric_value', 'activity_date', 'location', 'featured']);
        });

        Schema::table('causes', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};

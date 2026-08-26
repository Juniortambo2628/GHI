<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('email');
            });
        }

        if (! Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 100)->unique();
                $table->text('value')->nullable();
                $table->string('type', 30)->default('text');
                $table->string('group', 50)->default('general');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('page_views')) {
            Schema::create('page_views', function (Blueprint $table) {
                $table->id();
                $table->string('path', 191);
                $table->string('route_name', 100)->nullable();
                $table->string('referrer', 191)->nullable();
                $table->char('visitor_hash', 64)->nullable();
                $table->timestamp('occurred_at')->useCurrent();
                $table->index(['path', 'occurred_at']);
                $table->index('visitor_hash');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('site_settings');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};

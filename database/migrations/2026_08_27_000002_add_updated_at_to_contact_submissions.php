<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            } else {
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};

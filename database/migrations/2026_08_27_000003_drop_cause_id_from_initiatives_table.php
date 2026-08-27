<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('initiatives', function (Blueprint $table) {
            if (Schema::hasIndex('initiatives', 'initiatives_cause_id_foreign')) {
                $table->dropForeign('initiatives_cause_id_foreign');
            }
            $table->dropColumn('cause_id');
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('initiatives', function (Blueprint $table) {
            $table->foreignId('cause_id')->nullable()->constrained('causes')->nullOnDelete();
        });
    }
};

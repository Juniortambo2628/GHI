<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Causes
        Schema::create('causes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->text('quote')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('image', 255)->nullable();
            $table->integer('display_order')->default(0);
            $table->string('status', 20)->default('draft');
            $table->timestamps();
        });

        // Initiatives
        Schema::create('initiatives', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('category', 50);
            $table->foreignId('cause_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('draft');
            $table->timestamps();
            $table->index('status');
            $table->index('category');
            $table->index(['status', 'category']);
        });

        // Events
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('image', 255)->nullable();
            $table->dateTime('event_date');
            $table->string('location', 255)->nullable();
            $table->foreignId('initiative_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('draft');
            $table->timestamps();
            $table->index('status');
            $table->index('event_date');
            $table->index(['status', 'event_date']);
            $table->index(['initiative_id', 'status']);
        });

        // Impact Activities
        Schema::create('impact_activities', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 191)->nullable()->unique();
            $table->text('description')->nullable();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('people_affected')->default(0);
            $table->text('outcome_summary')->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->integer('display_order')->default(0);
            $table->string('metric_type', 50)->nullable();
            $table->decimal('metric_value', 10, 2)->nullable();
            $table->date('activity_date')->nullable();
            $table->string('location', 255)->nullable();
            $table->boolean('featured')->default(false);
            $table->string('status', 20)->default('draft');
            $table->timestamps();
            $table->index('status');
            $table->index('event_id');
            $table->index(['status', 'event_id']);
        });

        // Stories
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 191)->nullable();
            $table->text('content')->nullable();
            $table->string('author', 255)->nullable();
            $table->string('featured_image', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('category', 50)->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index('category');
            $table->index(['status', 'category']);
        });

        // Contact Submissions
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 255)->nullable();
            $table->string('lastname', 255)->nullable();
            $table->string('email', 255);
            $table->string('phone', 50)->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('message');
            $table->string('status', 20)->default('new');
            $table->timestamp('created_at')->useCurrent();
            $table->index('status');
            $table->index('created_at');
        });

        // Newsletter Subscribers
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->unique();
            $table->string('name', 255)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamps();
        });

        // Donations
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donor_name', 255)->nullable();
            $table->string('donor_email', 255)->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('donation_type', 50)->default('one-time');
            $table->string('transaction_id', 255)->nullable();
            $table->string('stripe_payment_intent_id', 255)->nullable();
            $table->string('status', 50)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('transaction_id');
            $table->index('status');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('contact_submissions');
        Schema::dropIfExists('stories');
        Schema::dropIfExists('impact_activities');
        Schema::dropIfExists('events');
        Schema::dropIfExists('initiatives');
        Schema::dropIfExists('causes');
    }
};

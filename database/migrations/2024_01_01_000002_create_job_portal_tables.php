<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $t) {
            $t->id(); $t->string('name')->unique(); $t->timestamps();
        });
        Schema::create('locations', function (Blueprint $t) {
            $t->id(); $t->string('name')->unique(); $t->timestamps();
        });
        Schema::create('skills', function (Blueprint $t) {
            $t->id(); $t->string('name')->unique(); $t->timestamps();
        });
        Schema::create('companies', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('website')->nullable();
            $t->string('logo')->nullable(); $t->text('description')->nullable(); $t->timestamps();
        });
        // NOTE: "jobs" is reserved by Laravel's queue, so we use job_listings
        Schema::create('job_listings', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('category_id')->constrained()->restrictOnDelete();
            $t->foreignId('location_id')->constrained()->restrictOnDelete();
            $t->string('job_type')->default('Full Time');
            $t->unsignedInteger('salary_min')->nullable();
            $t->unsignedInteger('salary_max')->nullable();
            $t->string('experience')->nullable();
            $t->text('description');
            $t->date('deadline')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('job_listing_skill', function (Blueprint $t) {
            $t->id();
            $t->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
            $t->foreignId('skill_id')->constrained()->cascadeOnDelete();
        });
        Schema::create('applications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name'); $t->string('email'); $t->string('phone')->nullable();
            $t->text('cover_letter')->nullable();
            $t->string('resume_path')->nullable();
            $t->string('status')->default('pending'); // pending, reviewed, shortlisted, rejected
            $t->timestamps();
        });
    }
    public function down(): void {
        foreach (['applications','job_listing_skill','job_listings','companies','skills','locations','categories'] as $x) Schema::dropIfExists($x);
    }
};

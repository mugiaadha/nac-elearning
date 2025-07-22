<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Site Settings Table Optimization
     */
    public function up(): void
    {
        // Optimize site_settings table structure and add indexes
        Schema::table('site_settings', function (Blueprint $table) {
            // Optimize data types for better performance
            $table->string('logo', 500)->nullable()->change(); // Allow longer paths
            $table->string('phone', 20)->nullable()->change(); // Optimize phone length
            $table->string('email', 100)->nullable()->change(); // Standard email length
            $table->string('facebook', 255)->nullable()->change();
            $table->string('twitter', 255)->nullable()->change();
            $table->string('copyright', 500)->nullable()->change();
            
            // Add new useful columns
            $table->string('instagram')->nullable()->after('twitter');
            $table->string('linkedin')->nullable()->after('instagram');
            $table->string('youtube')->nullable()->after('linkedin');
            $table->string('website_name')->nullable()->after('copyright');
            $table->text('meta_description')->nullable()->after('website_name');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->boolean('is_active')->default(true)->after('meta_keywords');
            
            // Add indexes for frequently accessed columns
            $table->index('email');
            $table->index('phone');
            $table->index('is_active');
            $table->index('created_at');
            $table->index('updated_at');
        });

        // Add a unique constraint to ensure only one active site setting
        Schema::table('site_settings', function (Blueprint $table) {
            $table->unique('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique(['is_active']);
            
            // Drop indexes
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            
            // Drop new columns
            $table->dropColumn([
                'instagram',
                'linkedin', 
                'youtube',
                'website_name',
                'meta_description',
                'meta_keywords',
                'is_active'
            ]);
        });
    }
};

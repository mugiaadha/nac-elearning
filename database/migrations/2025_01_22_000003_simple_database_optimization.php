<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Simple Database Optimization
     */
    public function up(): void
    {
        // Only add indexes to core tables that definitely exist
        
        // Users table optimization
        if (Schema::hasTable('users')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    // Only add index if column exists
                    if (Schema::hasColumn('users', 'status')) {
                        $table->index('status');
                    }
                    if (Schema::hasColumn('users', 'role')) {
                        $table->index('role');
                    }
                    if (Schema::hasColumn('users', 'email')) {
                        // Email already has unique constraint, so skip
                    }
                });
            } catch (\Exception $e) {
                // Ignore if index already exists
            }
        }

        // Courses table optimization
        if (Schema::hasTable('courses')) {
            try {
                Schema::table('courses', function (Blueprint $table) {
                    if (Schema::hasColumn('courses', 'status')) {
                        $table->index('status');
                    }
                    if (Schema::hasColumn('courses', 'category_id')) {
                        $table->index('category_id');
                    }
                    if (Schema::hasColumn('courses', 'instructor_id')) {
                        $table->index('instructor_id');
                    }
                });
            } catch (\Exception $e) {
                // Ignore if index already exists
            }
        }

        // Orders table optimization
        if (Schema::hasTable('orders')) {
            try {
                Schema::table('orders', function (Blueprint $table) {
                    if (Schema::hasColumn('orders', 'user_id')) {
                        $table->index('user_id');
                    }
                    if (Schema::hasColumn('orders', 'course_id')) {
                        $table->index('course_id');
                    }
                });
            } catch (\Exception $e) {
                // Ignore if index already exists
            }
        }

        // Site settings table optimization
        if (Schema::hasTable('site_settings')) {
            try {
                Schema::table('site_settings', function (Blueprint $table) {
                    if (Schema::hasColumn('site_settings', 'email')) {
                        $table->index('email');
                    }
                });
            } catch (\Exception $e) {
                // Ignore if index already exists
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes safely
        
        if (Schema::hasTable('site_settings')) {
            try {
                Schema::table('site_settings', function (Blueprint $table) {
                    $table->dropIndex(['email']);
                });
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
        }

        if (Schema::hasTable('orders')) {
            try {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropIndex(['user_id']);
                    $table->dropIndex(['course_id']);
                });
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
        }

        if (Schema::hasTable('courses')) {
            try {
                Schema::table('courses', function (Blueprint $table) {
                    $table->dropIndex(['status']);
                    $table->dropIndex(['category_id']);
                    $table->dropIndex(['instructor_id']);
                });
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
        }

        if (Schema::hasTable('users')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropIndex(['status']);
                    $table->dropIndex(['role']);
                });
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
        }
    }
};

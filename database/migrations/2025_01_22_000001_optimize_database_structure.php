<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations - Additional Database Optimizations
     */
    public function up(): void
    {
        // Optimize courses table data types
        Schema::table('courses', function (Blueprint $table) {
            // Change integer IDs to use proper foreign key types
            $table->unsignedBigInteger('category_id')->change();
            $table->unsignedBigInteger('subcategory_id')->change();
            $table->unsignedBigInteger('instructor_id')->change();
            
            // Optimize price columns
            $table->decimal('selling_price', 10, 2)->nullable()->change();
            $table->decimal('discount_price', 10, 2)->nullable()->change();
            
            // Add soft deletes for data integrity
            $table->softDeletes();
        });

        // Optimize orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id')->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('course_id')->nullable()->change();
            $table->unsignedBigInteger('instructor_id')->nullable()->change();
            
            // Optimize price column
            $table->decimal('price', 10, 2)->nullable()->change();
        });

        // Optimize payments table data types
        Schema::table('payments', function (Blueprint $table) {
            // Add proper foreign key types if not already defined
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }
            if (!Schema::hasColumn('payments', 'course_id')) {
                $table->unsignedBigInteger('course_id')->after('user_id');
            }
            if (!Schema::hasColumn('payments', 'instructor_id')) {
                $table->unsignedBigInteger('instructor_id')->after('course_id');
            }
            
            // Add status if not exists
            if (!Schema::hasColumn('payments', 'status')) {
                $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            }
        });

        // Optimize course_lectures table
        Schema::table('course_lectures', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->change();
        });

        // Optimize course_goals table
        Schema::table('course_goals', function (Blueprint $table) {
            if (!Schema::hasColumn('course_goals', 'course_id')) {
                $table->unsignedBigInteger('course_id')->after('id');
            } else {
                $table->unsignedBigInteger('course_id')->change();
            }
        });

        // Optimize course_sections table
        Schema::table('course_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('course_sections', 'course_id')) {
                $table->unsignedBigInteger('course_id')->after('id');
            } else {
                $table->unsignedBigInteger('course_id')->change();
            }
        });

        // Optimize reviews table
        Schema::table('reviews', function (Blueprint $table) {
            // Ensure proper data types
            if (!Schema::hasColumn('reviews', 'course_id')) {
                $table->unsignedBigInteger('course_id')->after('id');
            }
            if (!Schema::hasColumn('reviews', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('course_id');
            }
            if (!Schema::hasColumn('reviews', 'instructor_id')) {
                $table->unsignedBigInteger('instructor_id')->after('user_id');
            }
            if (!Schema::hasColumn('reviews', 'rating')) {
                $table->tinyInteger('rating')->unsigned();
            }
            if (!Schema::hasColumn('reviews', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active');
            }
        });

        // Optimize questions table
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }
            if (!Schema::hasColumn('questions', 'course_id')) {
                $table->unsignedBigInteger('course_id')->after('user_id');
            }
            if (!Schema::hasColumn('questions', 'instructor_id')) {
                $table->unsignedBigInteger('instructor_id')->after('course_id');
            }
        });

        // Optimize coupons table
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'instructor_id')) {
                $table->unsignedBigInteger('instructor_id')->after('id');
            }
            if (!Schema::hasColumn('coupons', 'course_id')) {
                $table->unsignedBigInteger('course_id')->after('instructor_id');
            }
            if (!Schema::hasColumn('coupons', 'status')) {
                $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            }
        });

        // Optimize blog_posts table
        Schema::table('blog_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('blog_posts', 'blogcat_id')) {
                $table->unsignedBigInteger('blogcat_id')->after('id');
            } else {
                $table->unsignedBigInteger('blogcat_id')->change();
            }
        });

        // Optimize chat_messages table
        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'sender_id')) {
                $table->unsignedBigInteger('sender_id')->after('id');
            }
            if (!Schema::hasColumn('chat_messages', 'receiver_id')) {
                $table->unsignedBigInteger('receiver_id')->after('sender_id');
            }
            
            // Add indexes for real-time features
            if (!Schema::hasColumn('chat_messages', 'is_read')) {
                $table->boolean('is_read')->default(false);
                $table->index('is_read');
            }
        });

        // Optimize sub_categories table
        Schema::table('sub_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_categories', 'category_id')) {
                $table->unsignedBigInteger('category_id')->after('id');
            } else {
                $table->unsignedBigInteger('category_id')->change();
            }
        });

        // Add full-text search indexes for better search performance
        if (Schema::hasTable('courses')) {
            DB::statement('ALTER TABLE courses ADD FULLTEXT search_index (course_title, course_name, description)');
        }

        if (Schema::hasTable('blog_posts')) {
            DB::statement('ALTER TABLE blog_posts ADD FULLTEXT search_index (post_title, post_tags, long_descp)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop full-text indexes
        if (Schema::hasTable('blog_posts')) {
            DB::statement('ALTER TABLE blog_posts DROP INDEX search_index');
        }

        if (Schema::hasTable('courses')) {
            DB::statement('ALTER TABLE courses DROP INDEX search_index');
        }

        // Reverse other changes would require careful consideration
        // as changing column types back might cause data loss
        
        Schema::table('courses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('chat_messages', 'is_read')) {
                $table->dropIndex(['is_read']);
                $table->dropColumn('is_read');
            }
        });
    }
};

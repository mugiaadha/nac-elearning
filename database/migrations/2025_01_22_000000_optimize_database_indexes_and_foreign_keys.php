<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Database Optimization
     */
    public function up(): void
    {
        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('status');
            $table->index(['status', 'role']); // Composite index for common queries
            $table->index('last_seen');
            $table->index('created_at');
        });

        // Add indexes and foreign keys to courses table
        Schema::table('courses', function (Blueprint $table) {
            // Add foreign key constraints
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('subcategory_id')->references('id')->on('sub_categories')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            
            // Add indexes for performance
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('instructor_id');
            $table->index('status');
            $table->index('featured');
            $table->index('bestseller');
            $table->index('highestrated');
            $table->index(['status', 'featured']); // Composite index
            $table->index(['category_id', 'status']);
            $table->index('created_at');
            $table->index('course_name_slug');
        });

        // Add indexes and foreign keys to course_sections table
        Schema::table('course_sections', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->index('course_id');
        });

        // Add indexes and foreign keys to course_lectures table
        Schema::table('course_lectures', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('course_sections')->onDelete('cascade');
            $table->index('course_id');
            $table->index('section_id');
            $table->index(['course_id', 'section_id']); // Composite index
        });

        // Add indexes and foreign keys to course_goals table
        Schema::table('course_goals', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->index('course_id');
        });

        // Add indexes and foreign keys to wishlists table
        Schema::table('wishlists', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->index('user_id');
            $table->index('course_id');
            $table->unique(['user_id', 'course_id']); // Prevent duplicate wishlists
        });

        // Add indexes and foreign keys to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index('payment_id');
            $table->index('user_id');
            $table->index('course_id');
            $table->index('instructor_id');
            $table->index('created_at');
            $table->index(['user_id', 'course_id']); // Composite index
        });

        // Add indexes and foreign keys to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('course_id');
            $table->index('instructor_id');
            $table->index('status');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
        });

        // Add indexes to reviews table
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index('course_id');
            $table->index('user_id');
            $table->index('instructor_id');
            $table->index('rating');
            $table->index('status');
            $table->index(['course_id', 'status']);
            $table->index('created_at');
        });

        // Add indexes to questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index('user_id');
            $table->index('course_id');
            $table->index('instructor_id');
            $table->index(['course_id', 'user_id']);
            $table->index('created_at');
        });

        // Add indexes to coupons table
        Schema::table('coupons', function (Blueprint $table) {
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            
            $table->index('instructor_id');
            $table->index('course_id');
            $table->index('coupon_name');
            $table->index('coupon_validity');
            $table->index('status');
            $table->index(['status', 'coupon_validity']);
        });

        // Add indexes to blog tables
        Schema::table('blog_categories', function (Blueprint $table) {
            $table->index('blog_category_slug');
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->foreign('blogcat_id')->references('id')->on('blog_categories')->onDelete('cascade');
            $table->index('blogcat_id');
            $table->index('post_slug');
            $table->index('created_at');
        });

        // Add indexes to chat_messages table
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index(['sender_id', 'receiver_id']);
            $table->index('created_at');
        });

        // Add indexes to categories and subcategories
        Schema::table('categories', function (Blueprint $table) {
            $table->index('category_slug');
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index('category_id');
            $table->index('subcategory_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys and indexes in reverse order
        
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['subcategory_slug']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['category_slug']);
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
            $table->dropIndex(['sender_id']);
            $table->dropIndex(['receiver_id']);
            $table->dropIndex(['sender_id', 'receiver_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropForeign(['blogcat_id']);
            $table->dropIndex(['blogcat_id']);
            $table->dropIndex(['post_slug']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('blog_categories', function (Blueprint $table) {
            $table->dropIndex(['blog_category_slug']);
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropForeign(['course_id']);
            $table->dropIndex(['instructor_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['coupon_name']);
            $table->dropIndex(['coupon_validity']);
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'coupon_validity']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['course_id']);
            $table->dropForeign(['instructor_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['instructor_id']);
            $table->dropIndex(['course_id', 'user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['instructor_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['instructor_id']);
            $table->dropIndex(['rating']);
            $table->dropIndex(['status']);
            $table->dropIndex(['course_id', 'status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['instructor_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['course_id']);
            $table->dropForeign(['instructor_id']);
            $table->dropIndex(['payment_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['instructor_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'course_id']);
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['course_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['course_id']);
            $table->dropUnique(['user_id', 'course_id']);
        });

        Schema::table('course_goals', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropIndex(['course_id']);
        });

        Schema::table('course_lectures', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['section_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['section_id']);
            $table->dropIndex(['course_id', 'section_id']);
        });

        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropIndex(['course_id']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['subcategory_id']);
            $table->dropForeign(['instructor_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['subcategory_id']);
            $table->dropIndex(['instructor_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['featured']);
            $table->dropIndex(['bestseller']);
            $table->dropIndex(['highestrated']);
            $table->dropIndex(['status', 'featured']);
            $table->dropIndex(['category_id', 'status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['course_name_slug']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'role']);
            $table->dropIndex(['last_seen']);
            $table->dropIndex(['created_at']);
        });
    }
};

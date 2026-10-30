<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['status', 'published_at'], 'posts_status_published_at_index');
            $table->index(['status', 'views_count'], 'posts_status_views_count_index');
        });

        Schema::table('post_views', function (Blueprint $table) {
            $table->index(['viewed_at', 'post_id'], 'post_views_viewed_at_post_id_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('show_on_homepage', 'categories_show_on_homepage_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_status_published_at_index');
            $table->dropIndex('posts_status_views_count_index');
        });

        Schema::table('post_views', function (Blueprint $table) {
            $table->dropIndex('post_views_viewed_at_post_id_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_show_on_homepage_index');
        });
    }
};

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
            $table->string('author_name')->nullable()->after('editor');
            $table->string('author_wa')->nullable()->after('author_name');
            $table->string('author_instansi')->nullable()->after('author_wa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['author_name', 'author_wa', 'author_instansi']);
        });
    }
};

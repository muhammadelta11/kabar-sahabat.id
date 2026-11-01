<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('client_name')->nullable()->after('name');
            $table->unsignedInteger('monthly_rate')->nullable()->after('is_active');
            $table->date('starts_at')->nullable()->after('monthly_rate');
            $table->date('ends_at')->nullable()->after('starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'monthly_rate', 'starts_at', 'ends_at']);
        });
    }
};

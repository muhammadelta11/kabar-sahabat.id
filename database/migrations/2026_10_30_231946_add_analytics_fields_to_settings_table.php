<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('ga_measurement_id')->nullable()->after('analytics_code');
            $table->string('ga_dashboard_url')->nullable()->after('ga_measurement_id');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['ga_measurement_id', 'ga_dashboard_url']);
        });
    }
};

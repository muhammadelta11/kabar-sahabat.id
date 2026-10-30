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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('whatsapp_enabled')->default(false)->after('phone');
            $table->string('whatsapp_number')->nullable()->after('whatsapp_enabled');
            $table->string('whatsapp_message')->nullable()->after('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_enabled', 'whatsapp_number', 'whatsapp_message']);
        });
    }
};

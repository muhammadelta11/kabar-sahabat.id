<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_status')->default('active')->after('role');
        });

        // Kontributor existing dianggap aktif agar tidak terkunci mendadak
        DB::table('users')
            ->where('role', 'kontributor')
            ->update(['account_status' => 'active']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_status');
        });
    }
};

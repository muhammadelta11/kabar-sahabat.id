<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        DB::table('users')->where('role', 'admin')->update(['role' => 'pimpinan_redaktur']);
        DB::table('users')->where('role', 'admin_konten')->update(['role' => 'pimpinan_redaktur']);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        DB::table('users')->where('role', 'pimpinan_redaktur')->update(['role' => 'admin']);
    }
};

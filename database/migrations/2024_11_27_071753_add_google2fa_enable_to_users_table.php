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
        if (Schema::hasTable('users')) {
            if (!Schema::hasColumn('users', 'google2fa_enable')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->integer('google2fa_enable')->default(0)->after('password');
                });
            }

            if (!Schema::hasColumn('users', 'google2fa_secret')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->text('google2fa_secret')->nullable()->after('google2fa_enable');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'google2fa_enable')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('google2fa_enable');
                });
            }

            if (Schema::hasColumn('users', 'google2fa_secret')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('google2fa_secret');
                });
            }
        }
    }
};

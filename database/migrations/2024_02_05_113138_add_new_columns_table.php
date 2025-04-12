<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->integer('is_enable_login')->after('type')->default(1);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->integer('is_enable_login')->after('telephone')->default(1);
        });

        Schema::table('workspaces', function (Blueprint $table) {
            $table->text('color_flag')->nullable()->after('theme_color');
        });
    }
    public function down(): void
    {
        //
    }
};

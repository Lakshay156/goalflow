<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('avatar')->nullable()->after('email');
            $table->text('bio')->nullable()->after('avatar');
            $table->string('theme')->default('dark')->after('bio');
            $table->integer('streak_days')->default(0)->after('theme');
            $table->timestamp('last_active_at')->nullable()->after('streak_days');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'bio', 'theme', 'streak_days', 'last_active_at']);
        });
    }
};

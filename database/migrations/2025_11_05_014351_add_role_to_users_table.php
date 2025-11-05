<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->enum('role', ['SELLER', 'MANAGER', 'STOCKIST', 'SUPERVISOR'])->default('SELLER');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('shop_id');
            $table->dropColumn('role');
            $table->dropSoftDeletes();
        });
    }
};

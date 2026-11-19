<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('name')->nullable()->after('title');
            $table->foreignId('category_id')->nullable()->after('user_id')
                ->constrained()->nullOnDelete();
        });

        DB::table('projects')->update(['name' => DB::raw('title')]);
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn('name');
        });

        Schema::dropIfExists('technologies');
        Schema::dropIfExists('categories');
    }
};

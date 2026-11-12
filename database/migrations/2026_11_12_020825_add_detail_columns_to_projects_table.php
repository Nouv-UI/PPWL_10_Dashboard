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
        chema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('short_description')->nullable()->after('slug');
            $table->text('description')->nullable()->after('short_description');
            $table->string('status')->default('draft')->after('description');
            $table->date('project_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('project_url')->nullable();
            $table->string('repository_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['slug']); 
            $table->dropColumn([ 
                'slug', 'short_description', 'description', 'status', 'project_date', 'is_featured', 'project_url', 'repository_url'
            ]);
        });
    }
};

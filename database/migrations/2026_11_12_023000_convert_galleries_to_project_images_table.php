<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('image');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('galleries')->orderBy('id')->get()->each(function ($gallery): void {
            DB::table('project_images')->insert([
                'id' => $gallery->id,
                'project_id' => $gallery->project_id,
                'image' => $gallery->image_path,
                'caption' => $gallery->caption,
                'sort_order' => 0,
                'created_at' => $gallery->created_at,
                'updated_at' => $gallery->updated_at,
            ]);
        });

        Schema::drop('galleries');
    }

    public function down(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        DB::table('project_images')->orderBy('id')->get()->each(function ($image): void {
            DB::table('galleries')->insert([
                'id' => $image->id,
                'project_id' => $image->project_id,
                'image_path' => $image->image,
                'caption' => $image->caption,
                'created_at' => $image->created_at,
                'updated_at' => $image->updated_at,
            ]);
        });

        Schema::drop('project_images');
    }
};

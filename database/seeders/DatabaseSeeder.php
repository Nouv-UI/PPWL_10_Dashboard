<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Skill;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'AdminRusdi',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $frontend = Skill::firstOrCreate(
            ['user_id' => $admin->id, 'name' => 'Laravel'],
            ['category' => 'Backend', 'description' => 'Pengembangan aplikasi web Laravel.']
        );
        $design = Skill::firstOrCreate(
            ['user_id' => $admin->id, 'name' => 'UI Design'],
            ['category' => 'Design', 'description' => 'Perancangan antarmuka aplikasi.']
        );

        $published = Project::firstOrCreate(
            ['user_id' => $admin->id, 'title' => 'Portfolio Dashboard'],
            ['description' => 'Contoh project untuk dashboard praktikum.', 'status' => 'published']
        );
        $draft = Project::firstOrCreate(
            ['user_id' => $admin->id, 'title' => 'Project Draft'],
            ['description' => 'Contoh project berstatus draft.', 'status' => 'draft']
        );
        $published->skills()->syncWithoutDetaching([$frontend->id, $design->id]);
        $draft->skills()->syncWithoutDetaching([$frontend->id]);
    }
}

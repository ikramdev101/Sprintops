<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('email', 'admin@sprintops.com')->first();
        $john = User::where('email', 'john@example.com')->first();
        $jane = User::where('email', 'jane@example.com')->first();

        // Projet 1
        $project1 = Project::create([
            'name' => 'Site Web E-commerce',
            'description' => 'Développement d\'un site web e-commerce pour vendre des produits en ligne',
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
            'status' => 'in_progress',
            'creator_id' => $admin->id,
        ]);

        $project1->members()->attach([
            $admin->id => ['role' => 'admin'],
            $john->id => ['role' => 'manager'],
            $jane->id => ['role' => 'member'],
        ]);

        // Projet 2
        $project2 = Project::create([
            'name' => 'Application Mobile',
            'description' => 'Développement d\'une application mobile pour iOS et Android',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(2),
            'status' => 'planning',
            'creator_id' => $john->id,
        ]);

        $project2->members()->attach([
            $john->id => ['role' => 'admin'],
            $jane->id => ['role' => 'member'],
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('email', 'admin@sprintops.com')->first();
        $john = User::where('email', 'john@example.com')->first();
        $jane = User::where('email', 'jane@example.com')->first();

        $project1 = Project::where('name', 'Site Web E-commerce')->first();
        $project2 = Project::where('name', 'Application Mobile')->first();

        // Tâches pour le projet 1
        Task::create([
            'title' => 'Conception de la base de données',
            'description' => 'Concevoir le schéma de la base de données pour le site e-commerce',
            'status' => 'done',
            'priority' => 'high',
            'due_date' => now()->addDays(5),
            'project_id' => $project1->id,
            'assignee_id' => $admin->id,
        ]);

        Task::create([
            'title' => 'Développement du frontend',
            'description' => 'Créer les interfaces utilisateur pour le site e-commerce',
            'status' => 'doing',
            'priority' => 'medium',
            'due_date' => now()->addDays(15),
            'project_id' => $project1->id,
            'assignee_id' => $jane->id,
        ]);

        Task::create([
            'title' => 'Intégration de la passerelle de paiement',
            'description' => 'Intégrer Stripe pour les paiements en ligne',
            'status' => 'to_do',
            'priority' => 'high',
            'due_date' => now()->addDays(20),
            'project_id' => $project1->id,
            'assignee_id' => $john->id,
        ]);

        // Tâches pour le projet 2
        Task::create([
            'title' => 'Wireframes de l\'application',
            'description' => 'Créer les wireframes pour l\'application mobile',
            'status' => 'doing',
            'priority' => 'medium',
            'due_date' => now()->addDays(7),
            'project_id' => $project2->id,
            'assignee_id' => $jane->id,
        ]);

        Task::create([
            'title' => 'Configuration de l\'environnement React Native',
            'description' => 'Configurer l\'environnement de développement pour React Native',
            'status' => 'to_do',
            'priority' => 'high',
            'due_date' => now()->addDays(3),
            'project_id' => $project2->id,
            'assignee_id' => $john->id,
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('creator')->get();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:planning,in_progress,completed,on_hold',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'creator_id' => $request->user()->id,
        ]);

        // Ajouter automatiquement le créateur comme membre avec le rôle admin
        $project->members()->attach($request->user()->id, ['role' => 'admin']);

        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        $project->load(['creator', 'members', 'tasks', 'teams']);
        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'sometimes|string|in:planning,in_progress,completed,on_hold',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project->update($request->all());

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(null, 204);
    }

    public function members(Project $project)
    {
        $members = $project->members;
        return response()->json($members);
    }

    public function addMember(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:member,manager,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Vérifier si l'utilisateur est déjà membre
        if ($project->members()->where('user_id', $request->user_id)->exists()) {
            return response()->json([
                'message' => 'User is already a member of this project',
            ], 422);
        }

        $project->members()->attach($request->user_id, ['role' => $request->role]);

        return response()->json([
            'message' => 'Member added successfully',
        ]);
    }

    public function removeMember(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Empêcher la suppression du créateur
        if ($project->creator_id == $request->user_id) {
            return response()->json([
                'message' => 'Cannot remove the project creator',
            ], 422);
        }

        $project->members()->detach($request->user_id);

        return response()->json([
            'message' => 'Member removed successfully',
        ]);
    }

    public function tasks(Project $project)
    {
        $tasks = $project->tasks()->with('assignee')->get();
        return response()->json($tasks);
    }

    public function statistics(Project $project)
    {
        $totalTasks = $project->tasks()->count();
        $todoTasks = $project->tasks()->where('status', 'to_do')->count();
        $doingTasks = $project->tasks()->where('status', 'doing')->count();
        $doneTasks = $project->tasks()->where('status', 'done')->count();
        $reviewTasks = $project->tasks()->where('status', 'review')->count();

        return response()->json([
            'total_tasks' => $totalTasks,
            'todo_tasks' => $todoTasks,
            'doing_tasks' => $doingTasks,
            'done_tasks' => $doneTasks,
            'review_tasks' => $reviewTasks,
            'completion_percentage' => $project->completion_percentage,
        ]);
    }
}
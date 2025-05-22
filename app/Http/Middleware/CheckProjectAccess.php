<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Project;

class CheckProjectAccess
{
    public function handle(Request $request, Closure $next)
    {
        $projectId = $request->route('project')->id ?? $request->project_id;
        
        if (!$projectId) {
            return $next($request);
        }

        $user = $request->user();
        $project = Project::findOrFail($projectId);

        // Vérifier si l'utilisateur est le créateur ou un membre du projet
        if ($project->creator_id === $user->id || $project->members()->where('user_id', $user->id)->exists()) {
            return $next($request);
        }

        return response()->json([
            'message' => 'You do not have access to this project',
        ], 403);
    }
}
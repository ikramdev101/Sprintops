<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with('owner')->get();
        return response()->json($teams);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => $request->user()->id,
        ]);

        $team->members()->attach($request->user()->id, ['role' => 'admin']);

        return response()->json($team, 201);
    }

    public function show(Team $team)
    {
        $team->load(['owner', 'members', 'projects']);
        return response()->json($team);
    }

    public function update(Request $request, Team $team)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $team->update($request->all());

        return response()->json($team);
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return response()->json(null, 204);
    }

    public function members(Team $team)
    {
        $members = $team->members;
        return response()->json($members);
    }

    public function addMember(Request $request, Team $team)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:member,manager,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($team->members()->where('user_id', $request->user_id)->exists()) {
            return response()->json([
                'message' => 'User is already a member of this team',
            ], 422);
        }

        $team->members()->attach($request->user_id, ['role' => $request->role]);

        return response()->json([
            'message' => 'Member added successfully',
        ]);
    }

    public function removeMember(Request $request, Team $team)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Empêcher la suppression du propriétaire
        if ($team->owner_id == $request->user_id) {
            return response()->json([
                'message' => 'Cannot remove the team owner',
            ], 422);
        }

        $team->members()->detach($request->user_id);

        return response()->json([
            'message' => 'Member removed successfully',
        ]);
    }

    public function projects(Team $team)
    {
        $projects = $team->projects;
        return response()->json($projects);
    }

    public function addProject(Request $request, Team $team)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Vérifier si le projet est déjà associé
        if ($team->projects()->where('project_id', $request->project_id)->exists()) {
            return response()->json([
                'message' => 'Project is already associated with this team',
            ], 422);
        }

        $team->projects()->attach($request->project_id);

        return response()->json([
            'message' => 'Project added successfully',
        ]);
    }

    public function removeProject(Request $request, Team $team)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $team->projects()->detach($request->project_id);

        return response()->json([
            'message' => 'Project removed successfully',
        ]);
    }
}
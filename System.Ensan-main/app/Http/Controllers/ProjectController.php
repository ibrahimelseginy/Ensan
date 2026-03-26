<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index() { return Project::paginate(20); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'fixed' => 'boolean',
            'status' => 'in:active,archived',
            'description' => 'nullable|string'
        ]);
        return Project::create($data);
    }
    public function show(Project $project) { return $project; }
    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'fixed' => 'boolean',
            'status' => 'in:active,archived',
            'description' => 'nullable|string'
        ]);
        $project->update($data);
        return $project;
    }
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->noContent();
    }
}

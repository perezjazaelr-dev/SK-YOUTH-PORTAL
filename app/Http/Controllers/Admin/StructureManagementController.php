<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Committee;
use App\Models\Initiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class StructureManagementController extends Controller
{
    /**
     * Display the structure management dashboard page.
     */
    public function index(): View
    {
        $committees = Committee::with(['initiatives' => function ($query) {
            $query->withCount('accomplishmentReports');
        }])->get();

        return view('admin.structure.index', compact('committees'));
    }

    /**
     * Store a new Committee (Subtopic).
     */
    public function storeCommittee(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:committees,name'],
        ]);

        $slug = Str::slug($request->input('name'));

        if (Committee::where('slug', $slug)->exists()) {
            return back()->withErrors(['name' => 'A committee with a similar name or slug already exists.'])->withInput();
        }

        $project = Project::first() ?? Project::create([
            'title' => 'SK Namayan Youth Services',
            'slug' => 'sk-namayan-youth-services',
            'description' => 'Comprehensive youth empowerment initiatives, community health programs, student research resources, and athletic leagues organized by the Sangguniang Kabataan of Barangay Namayan.'
        ]);

        Committee::create([
            'project_id' => $project->id,
            'name' => $request->input('name'),
            'slug' => $slug,
        ]);

        return back()->with('success', 'Committee (Subtopic) added successfully.');
    }

    /**
     * Delete a Committee (Subtopic).
     */
    public function destroyCommittee(Committee $committee): RedirectResponse
    {
        $committee->delete();

        return back()->with('success', 'Committee (Subtopic) and all its child initiatives deleted successfully.');
    }

    /**
     * Store a new Initiative (Project).
     */
    public function storeInitiative(Request $request): RedirectResponse
    {
        $request->validate([
            'committee_id' => ['required', 'exists:committees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'form_route' => ['nullable', 'string', 'max:255'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.label' => ['required_with:custom_fields', 'string', 'max:255'],
            'custom_fields.*.name' => ['required_with:custom_fields', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/'],
            'custom_fields.*.type' => ['required_with:custom_fields', 'string', 'in:text,textarea,number,date'],
            'custom_fields.*.placeholder' => ['nullable', 'string', 'max:255'],
        ]);

        $customFields = [];
        if ($request->has('custom_fields')) {
            foreach ($request->input('custom_fields') as $field) {
                $isRequired = isset($field['required']) && ($field['required'] == '1' || $field['required'] == 'true' || $field['required'] == 'on' || $field['required'] === true);
                $customFields[] = [
                    'label' => $field['label'],
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'placeholder' => $field['placeholder'] ?? '',
                    'required' => $isRequired,
                ];
            }
        }

        Initiative::create([
            'committee_id' => $request->input('committee_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'form_route' => $request->input('form_route'),
            'custom_fields' => $customFields,
        ]);

        return back()->with('success', 'Initiative (Project) added successfully.');
    }

    /**
     * Update an Initiative (Project).
     */
    public function updateInitiative(Request $request, Initiative $initiative): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'form_route' => ['nullable', 'string', 'max:255'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.label' => ['required_with:custom_fields', 'string', 'max:255'],
            'custom_fields.*.name' => ['required_with:custom_fields', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/'],
            'custom_fields.*.type' => ['required_with:custom_fields', 'string', 'in:text,textarea,number,date'],
            'custom_fields.*.placeholder' => ['nullable', 'string', 'max:255'],
        ]);

        $customFields = [];
        if ($request->has('custom_fields')) {
            foreach ($request->input('custom_fields') as $field) {
                $isRequired = isset($field['required']) && ($field['required'] == '1' || $field['required'] == 'true' || $field['required'] == 'on' || $field['required'] === true);
                $customFields[] = [
                    'label' => $field['label'],
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'placeholder' => $field['placeholder'] ?? '',
                    'required' => $isRequired,
                ];
            }
        }

        $initiative->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'form_route' => $request->input('form_route'),
            'custom_fields' => $customFields,
        ]);

        return back()->with('success', 'Initiative (Project) updated successfully.');
    }

    /**
     * Delete an Initiative (Project).
     */
    public function destroyInitiative(Initiative $initiative): RedirectResponse
    {
        $initiative->delete();

        return back()->with('success', 'Initiative (Project) deleted successfully.');
    }
}

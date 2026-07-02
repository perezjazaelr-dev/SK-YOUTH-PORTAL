<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RegistrationForm;
use App\Models\RegistrationResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SportsRegistrationController extends Controller
{
    /**
     * Show the public dynamic sports league registration form.
     */
    public function showRegistrationForm(Request $request): View
    {
        $divisions = RegistrationForm::with('league')->where('is_active', true)->get();
        
        $selectedDivisionId = $request->input('division_id');
        $selectedForm = null;

        if ($selectedDivisionId) {
            $selectedForm = RegistrationForm::with(['league', 'formFields'])->findOrFail($selectedDivisionId);
        } elseif ($divisions->isNotEmpty()) {
            $selectedForm = RegistrationForm::with(['league', 'formFields'])->findOrFail($divisions->first()->id);
        }

        return view('forms.sports-registration', compact('divisions', 'selectedForm'));
    }

    /**
     * Submit dynamic citizen responses.
     */
    public function submitRegistration(Request $request): RedirectResponse
    {
        $request->validate([
            'registration_form_id' => ['required', 'exists:registration_forms,id'],
        ]);

        $registrationForm = RegistrationForm::findOrFail($request->input('registration_form_id'));
        $fields = $registrationForm->formFields;

        $rules = [];
        $messages = [];
        
        foreach ($fields as $field) {
            $fieldRules = [];
            
            if ($field->is_required) {
                // To allow Laravel validation on nested array
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($field->field_type === 'number') {
                $fieldRules[] = 'numeric';
            } elseif ($field->field_type === 'date') {
                $fieldRules[] = 'date';
            } elseif ($field->field_type === 'file') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'max:10240'; // 10MB max
            }

            $rules['answers.' . $field->field_name] = $fieldRules;
            $messages['answers.' . $field->field_name . '.required'] = "The field '{$field->field_label}' is required.";
        }

        $validated = $request->validate($rules, $messages);

        $answers = $request->input('answers', []);

        foreach ($fields as $field) {
            if ($field->field_type === 'file') {
                if ($request->hasFile('answers.' . $field->field_name)) {
                    $file = $request->file('answers.' . $field->field_name);
                    $path = $file->store('sports-registrations', 'public');
                    $answers[$field->field_name] = $path;
                } else {
                    $answers[$field->field_name] = $answers[$field->field_name] ?? null;
                }
            }
        }

        RegistrationResponse::create([
            'registration_form_id' => $registrationForm->id,
            'citizen_name' => auth()->user() ? auth()->user()->name : 'Guest User',
            'citizen_email' => auth()->user() ? auth()->user()->email : 'guest@example.com',
            'answers' => $answers,
            'status' => 'Pending',
        ]);

        return redirect()->route('forms.sports.create', ['division_id' => $registrationForm->id])
            ->with('success', 'Your registration application has been submitted successfully.');
    }
}

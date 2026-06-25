<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{
    
    public function index(): View
    {
        $classes = SchoolClass::forCurrentYear()
            ->with('teacher')
            ->withCount(['enrollments as nb_eleves' => fn($q) => $q->actifs()])
            ->orderBy('niveau')
            ->get();

        return view('settings.classes.index', compact('classes'));
    }

    public function create(): View
    {
        $teachers = User::enseignants()->actifs()->orderBy('name')->get();
        $years    = AcademicYear::orderByDesc('date_debut')->get();
        $subjects = Subject::actives()->orderBy('nom')->get();
        return view('settings.classes.create', compact('teachers', 'years', 'subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        // $data = $request->validate([
        //     'academic_year_id'       => 'required|exists:academic_years,id',
        //     'niveau'                 => 'required|in:CP1,CP2,CE1,CE2,CM1,CM2',
        //     'nom'                    => 'required|string|max:50',
        //     'teacher_id'             => 'nullable|exists:users,id',
        //     'effectif_max'           => 'nullable|integer|min:1|max:100',
        //     'frais_inscription'      => 'required|numeric|min:0',
        //     'frais_scolarite_annuel' => 'required|numeric|min:0',
        //     'subjects'               => 'nullable|array',
        //     'subjects.*'             => 'exists:subjects,id',
        // ]);

        $data = $request->validate([
            'academic_year_id'       => 'required|exists:academic_years,id',
            'niveau'                 => 'required|in:CP1,CP2,CE1,CE2,CM1,CM2',
            'nom'                    => [
                'required',
                'string',
                'max:50',
                // Unicité : même nom interdit pour la même année scolaire
                Rule::unique('school_classes', 'nom')
                    ->where('academic_year_id', $request->academic_year_id),
            ],
            'teacher_id'             => 'nullable|exists:users,id',
            'effectif_max'           => 'nullable|integer|min:1|max:100',
            'frais_inscription'      => 'required|numeric|min:0',
            'frais_scolarite_annuel' => 'required|numeric|min:0',
            'subjects'               => 'nullable|array',
            'subjects.*'             => 'exists:subjects,id',
        ], [
            // Message d'erreur en français
            'nom.unique' => 'Une classe avec ce nom existe déjà pour cette année scolaire.',
        ]);

        // Créer la classe
        $classe = SchoolClass::create($data);

        // Associer les matières choisies
        if ($request->filled('subjects')) {
            foreach ($request->subjects as $subjectId) {
                $subject = Subject::find($subjectId);
                $classe->subjects()->attach($subjectId, [
                    'coefficient' => $subject->coefficient,
                    'teacher_id'  => $classe->teacher_id,
                ]);
            }
        }


        return redirect()
            ->route('settings.classes.index')
            ->with('success', "Classe {$data['nom']} créée avec succès.");
    }

    public function show(SchoolClass $schoolClass): View
    {
        $schoolClass->load(['teacher', 'subjects', 'enrollments.student']);
        return view('settings.classes.show', compact('schoolClass'));
    }

    public function edit(SchoolClass $schoolClass): View
    {
        $teachers = User::enseignants()->actifs()->orderBy('name')->get();
        $subjects = Subject::actives()->orderBy('nom')->get(); // ← ajouter
        $schoolClass->load('subjects'); // ← charger les matières existantes
        return view('settings.classes.edit', compact('schoolClass', 'teachers', 'subjects'));
    }

    public function update(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        // $data = $request->validate([
        //     'nom'                    => 'required|string|max:50',
        //     'teacher_id'             => 'nullable|exists:users,id',
        //     'effectif_max'           => 'nullable|integer|min:1|max:100',
        //     'frais_inscription'      => 'required|numeric|min:0',
        //     'frais_scolarite_annuel' => 'required|numeric|min:0',
        //     'subjects'               => 'nullable|array',
        //     'subjects.*'             => 'exists:subjects,id',
        // ]);

        $data = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:50',
                // Ignorer la classe actuelle lors de la modification
                Rule::unique('school_classes', 'nom')
                    ->where('academic_year_id', $schoolClass->academic_year_id)
                    ->ignore($schoolClass->id),
            ],
            'teacher_id'             => 'nullable|exists:users,id',
            'effectif_max'           => 'nullable|integer|min:1|max:100',
            'frais_inscription'      => 'required|numeric|min:0',
            'frais_scolarite_annuel' => 'required|numeric|min:0',
            'subjects'               => 'nullable|array',
            'subjects.*'             => 'exists:subjects,id',
        ], [
            'nom.unique' => 'Une classe avec ce nom existe déjà pour cette année scolaire.',
        ]);

        $schoolClass->update($data);

        // Synchroniser les matières (remplace les anciennes)
        $sync = [];
        foreach ($request->subjects ?? [] as $subjectId) {
            $subject = Subject::find($subjectId);
            $sync[$subjectId] = [
                'coefficient' => $subject->coefficient,
                'teacher_id'  => $schoolClass->teacher_id,
            ];
        }
        $schoolClass->subjects()->sync($sync);

        return redirect()
            ->route('settings.classes.index')
            ->with('success', 'Classe mise à jour.');
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        $this->authorize('delete', $schoolClass);
        $schoolClass->delete();

        return redirect()
            ->route('settings.classes.index')
            ->with('success', 'Classe supprimée.');
    }

    /** Synchronisation des matières attribuées à une classe */
    public function syncSubjects(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $request->validate([
            'subjects'              => 'array',
            'subjects.*'            => 'exists:subjects,id',
            'coefficients'          => 'array',
            'coefficients.*'        => 'nullable|numeric|min:0',
            'teacher_ids'           => 'array',
            'teacher_ids.*'         => 'nullable|exists:users,id',
        ]);

        $sync = [];
        foreach ($request->subjects ?? [] as $subjectId) {
            $sync[$subjectId] = [
                'coefficient' => $request->coefficients[$subjectId] ?? null,
                'teacher_id'  => $request->teacher_ids[$subjectId] ?? null,
            ];
        }

        $schoolClass->subjects()->sync($sync);

        return back()->with('success', 'Matières de la classe mises à jour.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{

    // -------------------------------------------------------------------------
    // Liste des élèves
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $query = Student::query()
            ->actifs()
            ->with('currentEnrollment.schoolClass')
            ->when($request->filled('search'), fn($q) => $q->search($request->search))
            ->when($request->filled('classe'), function ($q) use ($request) {
                $q->whereHas('currentEnrollment', fn($sq) =>
                    $sq->where('class_id', $request->classe)
                );
            })
            ->when($request->filled('sexe'), fn($q) => $q->where('sexe', $request->sexe));

        $students = $query->orderBy('nom')->orderBy('prenom')->paginate(20)->withQueryString();
        $classes  = SchoolClass::forCurrentYear()->orderBy('niveau')->get();

        return view('students.index', compact('students', 'classes'));
    }

    // -------------------------------------------------------------------------
    // Formulaire de création
    // -------------------------------------------------------------------------

    public function create(): View
    {
        $classes = SchoolClass::forCurrentYear()->orderBy('niveau')->get();
        return view('students.create', compact('classes'));
    }

    // -------------------------------------------------------------------------
    // Enregistrement d'un nouvel élève
    // -------------------------------------------------------------------------

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom'               => 'required|string|max:100',
            'prenom'            => 'required|string|max:100',
            'date_naissance'    => 'required|date|before:today',
            'lieu_naissance'    => 'nullable|string|max:100',
            'sexe'              => 'required|in:M,F',
            'photo'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nom_pere'          => 'nullable|string|max:150',
            'nom_mere'          => 'nullable|string|max:150',
            'tuteur_nom'        => 'nullable|string|max:150',
            'tuteur_telephone'  => 'nullable|string|max:20',
            'tuteur_telephone2' => 'nullable|string|max:20',
            'adresse'           => 'nullable|string|max:255',
            'class_id'          => 'required|exists:school_classes,id',
        ]);

        // Sauvegarder class_id AVANT de le supprimer
        $classId = $data['class_id'];
        unset($data['class_id']);

        // Upload photo
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')
                ->store('students/photos', 'public');
        }

        $student = Student::create($data);

        // Créer l'inscription avec le class_id sauvegardé
        Enrollment::create([
            'student_id'       => $student->id,
            'class_id'         => $classId,
            'academic_year_id' => AcademicYear::active()->id,
            'date_inscription' => today(),
            'statut'           => 'actif',
            'created_by'       => auth()->id(),
        ]);

        return redirect()
            ->route('students.show', $student)
            ->with('success', "L'élève {$student->nom_complet} a été inscrit avec succès.");
    }

    // -------------------------------------------------------------------------
    // Fiche détaillée d'un élève
    // -------------------------------------------------------------------------

    public function show(Student $student): View
    {
        $student->load([
            'currentEnrollment.schoolClass',
            'enrollments.academicYear',
            'payments' => fn($q) => $q->valides()->latest('date_paiement'),
        ]);

        $resteAPayer   = $student->resteAPayer();
        $totalPaye     = $student->totalPaye();
        $fraisTotal    = $student->currentEnrollment?->schoolClass?->frais_scolarite_annuel ?? 0;

        return view('students.show', compact('student', 'resteAPayer', 'totalPaye', 'fraisTotal'));
    }

    // -------------------------------------------------------------------------
    // Formulaire de modification
    // -------------------------------------------------------------------------

    public function edit(Student $student): View
    {
        $classes = SchoolClass::forCurrentYear()->orderBy('niveau')->get();
        return view('students.edit', compact('student', 'classes'));
    }

    // -------------------------------------------------------------------------
    // Mise à jour d'un élève
    // -------------------------------------------------------------------------

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'nom'               => 'required|string|max:100',
            'prenom'            => 'required|string|max:100',
            'date_naissance'    => 'required|date|before:today',
            'lieu_naissance'    => 'nullable|string|max:100',
            'sexe'              => 'required|in:M,F',
            'photo'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nom_pere'          => 'nullable|string|max:150',
            'nom_mere'          => 'nullable|string|max:150',
            'tuteur_nom'        => 'nullable|string|max:150',
            'tuteur_telephone'  => 'nullable|string|max:20',
            'tuteur_telephone2' => 'nullable|string|max:20',
            'adresse'           => 'nullable|string|max:255',
        ]);

        // Remplacement de la photo
        if ($request->hasFile('photo')) {
            if ($student->photo_path) {
                Storage::disk('public')->delete($student->photo_path);
            }
            $data['photo_path'] = $request->file('photo')
                ->store('students/photos', 'public');
        }

        $student->update($data);

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Informations mises à jour avec succès.');
    }

    // -------------------------------------------------------------------------
    // Suppression (soft delete)
    // -------------------------------------------------------------------------

    public function destroy(Student $student): RedirectResponse
    {
        $student->update(['is_active' => false]);
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', "L'élève {$student->nom_complet} a été supprimé.");
    }

    // -------------------------------------------------------------------------
    // Carte / fiche rapide (utilisée dans les modales)
    // -------------------------------------------------------------------------

    public function card(Student $student): View
    {
        $student->load('currentEnrollment.schoolClass');
        return view('students.partials.card', compact('student'));
    }

    // -------------------------------------------------------------------------
    // Activer / désactiver un élève
    // -------------------------------------------------------------------------

    public function toggleActive(Student $student): RedirectResponse
    {
        $student->update(['is_active' => ! $student->is_active]);
        $status = $student->is_active ? 'activé' : 'désactivé';

        return back()->with('success', "Compte élève {$status}.");
    }
}

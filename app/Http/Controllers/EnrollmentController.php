<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{

    public function index(Request $request): View
    {
        $enrollments = Enrollment::with(['student', 'schoolClass', 'academicYear'])
            ->forCurrentYear()
            ->when($request->filled('school_classes'), fn($q) => $q->where('classes_id', $request->classe))
            ->when($request->filled('statut'), fn($q) => $q->where('statut', $request->statut))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $classes = SchoolClass::forCurrentYear()->orderBy('niveau')->get();

        return view('enrollments.index', compact('enrollments', 'school_classes'));
    }

    public function create(): View
    {
        $students = Student::actifs()
            ->whereDoesntHave('enrollments', fn($q) => $q->forCurrentYear())
            ->orderBy('nom')
            ->get();

        $classes = SchoolClass::forCurrentYear()->orderBy('niveau')->get();

        return view('enrollments.create', compact('students', 'school_classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'classes_id'         => 'required|exists:classes,id',
            'date_inscription' => 'required|date',
        ]);

        $year = AcademicYear::active();

        // Vérification : élève déjà inscrit cette année
        $exists = Enrollment::where('student_id', $data['student_id'])
            ->where('academic_year_id', $year->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['student_id' => 'Cet élève est déjà inscrit pour cette année scolaire.']);
        }

        // Vérification capacité de la classe
        $classe = SchoolClass::findOrFail($data['class_id']);
        if ($classe->effectif_actuel >= $classe->effectif_max) {
            return back()->withErrors(['class_id' => "La classe {$classe->nom} est complète (max {$classe->effectif_max} élèves)."]);
        }

        Enrollment::create([
            ...$data,
            'academic_year_id' => $year->id,
            'statut'           => 'actif',
            'created_by'       => auth()->id(),
        ]);

        return redirect()
            ->route('enrollments.index')
            ->with('success', 'Inscription enregistrée avec succès.');
    }

    public function show(Enrollment $enrollment): View
    {
        $enrollment->load(['student', 'schoolClass', 'academicYear', 'payments', 'grades.subject', 'grades.term']);
        return view('enrollments.show', compact('enrollment'));
    }

    /** Changement de statut (transfert, exclusion, etc.) */
    public function updateStatus(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $data = $request->validate([
            'statut'        => 'required|in:actif,transfere,exclu,diplome',
            'motif_depart'  => 'nullable|string|max:255',
        ]);

        $enrollment->update($data);

        return back()->with('success', 'Statut de l\'inscription mis à jour.');
    }

    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        // Empêcher la suppression si des paiements existent
        if ($enrollment->payments()->count() > 0) {
            return back()->withErrors(['error' => 'Impossible de supprimer une inscription avec des paiements enregistrés.']);
        }

        $enrollment->delete();

        return redirect()
            ->route('enrollments.index')
            ->with('success', 'Inscription supprimée.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Absence::with(['student', 'createdBy'])
                        ->orderBy('date', 'desc');

        // Filtre par classe
        if ($request->filled('class_id')) {
            $query->whereHas('student.enrollments', function ($q) use ($request) {
                $q->where('class_id', $request->class_id)
                  ->where('statut', 'actif');
            });
        }

        // Filtre par nom
        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%');
            });
        }

        $absences = $query->paginate(15);
        $classes = SchoolClass::orderBy('niveau')->get();

        return view('absences.index', compact('absences', 'classes'));
    }

    public function create(Request $request)
    {
        $query = Student::actifs()->orderBy('nom');

        // Filtre par classe
        if ($request->filled('class_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('class_id', $request->class_id)
                  ->where('statut', 'actif');
            });
        }

        // Filtre par nom
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->get();
        $classes = SchoolClass::orderBy('niveau')->get();

        return view('absences.create', compact('students', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date'       => 'required|date',
            'motif'      => 'nullable|string|max:255',
            'justifie'   => 'boolean',
        ]);

        Absence::create([
            'student_id' => $request->student_id,
            'date'       => $request->date,
            'motif'      => $request->motif,
            'justifie'   => $request->boolean('justifie'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('absences.index')
                         ->with('success', 'Absence enregistrée avec succès.');
    }

    public function edit(Request $request, Absence $absence)
    {
        $query = Student::actifs()->orderBy('nom');

        // Filtre par classe
        if ($request->filled('class_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('class_id', $request->class_id)
                  ->where('statut', 'actif');
            });
        }

        // Filtre par nom
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->get();
        $classes = SchoolClass::orderBy('niveau')->get();

        return view('absences.edit', compact('absence', 'students', 'classes'));
    }

    public function update(Request $request, Absence $absence)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date'       => 'required|date',
            'motif'      => 'nullable|string|max:255',
            'justifie'   => 'boolean',
        ]);

        $absence->update([
            'student_id' => $request->student_id,
            'date'       => $request->date,
            'motif'      => $request->motif,
            'justifie'   => $request->boolean('justifie'),
        ]);

        return redirect()->route('absences.index')
                         ->with('success', 'Absence modifiée avec succès.');
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();

        return redirect()->route('absences.index')
                         ->with('success', 'Absence supprimée avec succès.');
    }
}
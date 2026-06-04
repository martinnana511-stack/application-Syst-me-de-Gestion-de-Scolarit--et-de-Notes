<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\ReportCard;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Term;
use App\Services\GradeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{

    private GradeService $gradeService;

    public function __construct(GradeService $gradeService)
    {
        $this->gradeService =$gradeService;
    }

    // -------------------------------------------------------------------------
    // Page de sélection classe + trimestre
    // -------------------------------------------------------------------------

    public function index(): View
    {
        $classes = SchoolClass::forCurrentYear()
            ->when(auth()->user()->isEnseignant(), fn($q) => $q->where('teacher_id', auth()->id()))
            ->orderBy('niveau')
            ->get();

        $terms = Term::forCurrentYear()->orderBy('numero')->get();

        return view('grades.index', compact('classes', 'terms'));
    }

    // -------------------------------------------------------------------------
    // Grille de notes d'une classe pour un trimestre
    // -------------------------------------------------------------------------

    public function classGrades(SchoolClass $class, Term $term): View
    {
        // Vérification : enseignant ne peut voir que ses classes
        if (auth()->user()->isEnseignant() && $class->teacher_id !== auth()->id()) {
            abort(403);
        }

        //jointure directe
        $enrollments = Enrollment::with('student')
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->where('enrollments.class_id', $class->id)
            ->where('enrollments.statut', 'actif')
            ->orderBy('students.nom')
            ->orderBy('students.prenom')
            ->select('enrollments.*')
            ->get();

        $subjects = $class->subjects()->actives()->orderBy('nom')->get();

        // Charger toutes les notes existantes
        $existingGrades = Grade::where('term_id', $term->id)
            ->whereIn('enrollment_id', $enrollments->pluck('id'))
            ->get()
            ->keyBy(fn($g) => "{$g->enrollment_id}_{$g->subject_id}");

        return view('grades.class', compact('class', 'term', 'enrollments', 'subjects', 'existingGrades'));
    }

    // -------------------------------------------------------------------------
    // Saisie individuelle
    // -------------------------------------------------------------------------

    public function create(Request $request): View
    {
        $classes  = SchoolClass::forCurrentYear()->orderBy('niveau')->get();
        $subjects = Subject::actives()->orderBy('nom')->get();
        $terms    = Term::forCurrentYear()->open()->get();

        return view('grades.create', compact('classes', 'subjects', 'terms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'subject_id'    => 'required|exists:subjects,id',
            'term_id'       => 'required|exists:terms,id',
            'note'          => 'required_unless:is_absent,1|nullable|numeric|min:0|max:20',
            'note_max'      => 'nullable|integer|min:1|max:100',
            'appreciation'  => 'nullable|string|max:255',
            'is_absent'     => 'boolean',
        ]);

        $this->authorize('create', Grade::class);

        $term = Term::findOrFail($data['term_id']);
        if ($term->is_closed) {
            return back()->withErrors(['term_id' => 'Ce trimestre est clôturé. Les notes ne peuvent plus être modifiées.']);
        }

        $grade = Grade::updateOrCreate(
            [
                'enrollment_id' => $data['enrollment_id'],
                'subject_id'    => $data['subject_id'],
                'term_id'       => $data['term_id'],
            ],
            [
                'note'         => $data['is_absent'] ?? false ? null : $data['note'],
                'note_max'     => $data['note_max'] ?? 20,
                'appreciation' => $data['appreciation'] ?? null,
                'is_absent'    => $data['is_absent'] ?? false,
                'created_by'   => auth()->id(),
                'updated_by'   => auth()->id(),
            ]
        );

        // Recalcul automatique de la moyenne après chaque saisie
        $this->gradeService->recalculerMoyenne($data['enrollment_id'], $data['term_id']);

        return back()->with('success', 'Note enregistrée avec succès.');
    }

   
    public function bulkStore(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'term_id'    => 'required|exists:terms,id',
                'class_id'   => 'required|exists:school_classes,id',
                'subject_id' => 'required|exists:subjects,id',
                'notes'      => 'required|array',
            ]);

            $term = Term::findOrFail($request->term_id);
            if ($term->is_closed) {
                return response()->json(['error' => 'Trimestre clôturé.'], 422);
            }

            // Récupérer la note maximale de la matière
            $subject = Subject::findOrFail($request->subject_id);
            $noteMax = $subject->note_max;

            $saved = 0;
            // Valider chaque note
            foreach ($request->notes as $row) {
                $note = $row['note'] ?? null;
                $isAbsent = $row['is_absent'] ?? false;

                // Vérification avec la note max de la matière
                if (!$isAbsent && $note !== null && $note != '') {
                    if ((float)$note < 0 || (float)$note > $noteMax) {
                        return response()->json([
                            'error' => "Note invalide : Les notes doive être comprise entre 0 et {$noteMax} pour la matière {$subject->nom}."
                        ], 422);
                    }
                }

                Grade::updateOrCreate(
                    [
                        'enrollment_id' => $row['enrollment_id'],
                        'subject_id'    => $request->subject_id,
                        'term_id'       => $request->term_id,
                    ],
                    [
                        'note'         => $isAbsent ? 0 : $note,
                        'note_max'     => $noteMax,
                        'appreciation' => $row['appreciation'] ?? null,
                        'is_absent'    => $isAbsent,
                        'updated_by'   => auth()->id(),
                        'created_by'   => auth()->id(),
                    ]
                );
                $saved++;
            }

            // Recalcul des moyennes
            $enrollmentIds = Enrollment::where('class_id', $request->class_id)
                ->where('statut', 'actif')
                ->pluck('id');

            foreach ($enrollmentIds as $enrollmentId) {
                $this->gradeService->recalculerMoyenne($enrollmentId, $request->term_id);
            }

            return response()->json([
                'success' => true,
                'message' => "{$saved} note(s) enregistrée(s).",
            ]);

        } catch (\Exception $e) {
            \Log::error('bulkStore error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Modification d'une note
    // -------------------------------------------------------------------------

    public function edit(Grade $grade): View
    {
        $this->authorize('update', $grade);
        $subjects = Subject::actives()->get();
        $terms    = Term::forCurrentYear()->get();
        return view('grades.edit', compact('grade', 'subjects', 'terms'));
    }

    public function update(Request $request, Grade $grade): RedirectResponse
    {
        $this->authorize('update', $grade);

        $data = $request->validate([
            'note'         => 'required_unless:is_absent,1|nullable|numeric|min:0|max:20',
            'note_max'     => 'nullable|integer|min:1|max:100',
            'appreciation' => 'nullable|string|max:255',
            'is_absent'    => 'boolean',
        ]);

        $grade->update([
            ...$data,
            'note'       => ($data['is_absent'] ?? false) ? null : $data['note'],
            'updated_by' => auth()->id(),
        ]);

        $this->gradeService->recalculerMoyenne($grade->enrollment_id, $grade->term_id);

        return back()->with('success', 'Note mise à jour.');
    }

    // -------------------------------------------------------------------------
    // Suppression d'une note
    // -------------------------------------------------------------------------

    public function destroy(Grade $grade): RedirectResponse
    {
        $this->authorize('delete', $grade);
        $enrollmentId = $grade->enrollment_id;
        $termId       = $grade->term_id;
        $grade->delete();

        $this->gradeService->recalculerMoyenne($enrollmentId, $termId);

        return back()->with('success', 'Note supprimée.');
    }

    // -------------------------------------------------------------------------
    // Export Excel/CSV des notes d'une classe
    // -------------------------------------------------------------------------

    public function export(SchoolClass $class, Term $term)
    {
        $enrollments = Enrollment::with(['student', 'grades' => fn($q) => $q->where('term_id', $term->id)->with('subject')])
            ->where('class_id', $class->id)
            ->actifs()
            ->get()
            ->sortBy('student.nom');

        $subjects = $class->subjects()->actives()->orderBy('nom')->get();

        // Génération CSV simple
        $filename = "notes_{$class->nom}_{$term->libelle}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($enrollments, $subjects) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            // En-têtes
            $header = ['Matricule', 'Nom', 'Prénom', 'Sexe'];
            foreach ($subjects as $s) {
                $header[] = $s->nom . ' /' . $s->note_max;
            }
            $header[] = 'Moyenne Générale';
            $header[] = 'Rang';
            fputcsv($handle, $header, ';');

            // Données
            foreach ($enrollments as $enrollment) {
                $row = [
                    $enrollment->student->matricule,
                    $enrollment->student->nom,
                    $enrollment->student->prenom,
                    $enrollment->student->sexe,
                ];
                $gradeMap = $enrollment->grades->keyBy('subject_id');
                foreach ($subjects as $s) {
                    $g = $gradeMap->get($s->id);
                    $row[] = $g ? ($g->is_absent ? 'ABS' : $g->note) : '';
                }
                $rc = $enrollment->reportCards()->where('term_id', request()->route('term'))->first();
                $row[] = $rc?->moyenne_generale ?? '';
                $row[] = $rc?->rang ?? '';
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\ReportCard;
use App\Models\SchoolClass;
use App\Models\Term;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportCardController extends Controller
{

    private GradeService $gradeService;

    public function __construct(GradeService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    // -------------------------------------------------------------------------
    // Sélecteur classe + trimestre
    // -------------------------------------------------------------------------

    public function index(): View
    {
        $classes = SchoolClass::forCurrentYear()
            ->when(auth()->user()->isEnseignant(), fn($q) => $q->where('teacher_id', auth()->id()))
            ->orderBy('niveau')
            ->get();

        $terms = Term::forCurrentYear()->orderBy('numero')->get();

        return view('report-cards.index', compact('classes', 'terms'));
    }

    // -------------------------------------------------------------------------
    // Classement d'une classe pour un trimestre
    // -------------------------------------------------------------------------

    public function show(Request $request): View
    {
        $class = SchoolClass::findOrFail($request->class_id);
        $term  = Term::findOrFail($request->term_id);

        $reportCards = ReportCard::with(['enrollment.student'])
            ->whereHas('enrollment', fn($q) => $q->where('class_id', $class->id)->actifs())
            ->where('term_id', $term->id)
            ->orderBy('rang')
            ->get();

        $stats = [
            'effectif'          => $reportCards->count(),
            'moyenne_classe'    => $reportCards->whereNotNull('moyenne_generale')->avg('moyenne_generale'),
            'meilleure_moyenne' => $reportCards->max('moyenne_generale'),
            'plus_faible'       => $reportCards->min('moyenne_generale'),
            'nb_mentions'       => $reportCards->groupBy('mention')->map->count(),
        ];

        return view('report-cards.show', compact('class', 'term', 'reportCards', 'stats'));
    }

    // -------------------------------------------------------------------------
    // Bulletin individuel PDF
    // -------------------------------------------------------------------------

    public function pdf(Enrollment $enrollment, Term $term): Response
    {
        $enrollment->load(['student', 'schoolClass.subjects', 'academicYear']);

        $grades = \App\Models\Grade::where('enrollment_id', $enrollment->id)
            ->where('term_id', $term->id)
            ->with('subject')
            ->get()
            ->keyBy('subject_id');

        $reportCard = ReportCard::firstOrCreate(
            ['enrollment_id' => $enrollment->id, 'term_id' => $term->id],
            ['calculated_at' => now()]
        );

        $reportCard->recalculate();

        $subjects = $enrollment->schoolClass->subjects()->actives()->orderBy('nom')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('report-cards.pdf', compact(
            'enrollment', 'term', 'grades', 'reportCard', 'subjects'
        ))->setPaper('A4', 'portrait');

        return $pdf->download("bulletin_{$enrollment->student->matricule}_{$term->libelle}.pdf");
    }

    // -------------------------------------------------------------------------
    // Recalcul des moyennes et rangs d'une classe
    // -------------------------------------------------------------------------

    public function recalculate(Request $request, SchoolClass $class, Term $term): RedirectResponse
    {
        # $this->middleware('role:gestionnaire');

        $enrollments = Enrollment::where('class_id', $class->id)->actifs()->pluck('id');

        foreach ($enrollments as $enrollmentId) {
            $this->gradeService->recalculerMoyenne($enrollmentId, $term->id);
        }

        ReportCard::recalculateRanks($class->id, $term->id);

        return back()->with('success', "Moyennes et classement recalculés pour {$class->nom} — {$term->libelle}.");
    }

    // -------------------------------------------------------------------------
    // Publication des bulletins (rendre visible)
    // -------------------------------------------------------------------------

    public function publish(Enrollment $enrollment, Term $term): RedirectResponse
    {
        $this->authorize('manage-admin');

        ReportCard::where('enrollment_id', $enrollment->id)
            ->where('term_id', $term->id)
            ->update(['is_published' => true]);

        return back()->with('success', 'Bulletin publié.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // -------------------------------------------------------------------------
    // Vue principale du tableau de bord
    // -------------------------------------------------------------------------

    public function index(): View
    {
        $year = AcademicYear::active();

        $stats = $this->buildStats($year);

        $impayes = $this->getImpayes($year, limit: 10);

        $parClasse = $this->getStatsParClasse($year);

        return view('dashboard.index', compact('year', 'stats', 'impayes', 'parClasse'));
    }

    // -------------------------------------------------------------------------
    // Endpoint AJAX : statistiques globales (pour Chart.js)
    // -------------------------------------------------------------------------

    public function stats(): JsonResponse
    {
        $year = AcademicYear::active();
        return response()->json($this->buildStats($year));
    }

    // -------------------------------------------------------------------------
    // Liste complète des impayés (page dédiée)
    // -------------------------------------------------------------------------

    public function impayes(Request $request): View
    {
        $year = AcademicYear::active();

        $query = Enrollment::query()
            ->with(['student', 'schoolClass'])
            ->forCurrentYear()
            ->actifs()
            ->when($request->filled('classe'), fn($q) => $q->where('class_id', $request->classe))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('student', fn($sq) =>
                    $sq->search($request->search)
                );
            });

        // Filtrer uniquement les élèves ayant un reste à payer
        $enrollments = $query->get()->filter(fn($e) => $e->reste > 0);

        // Pagination manuelle après filtrage
        $page        = $request->get('page', 1);
        $perPage     = 20;
        $total       = $enrollments->count();
        $impayes     = $enrollments->forPage($page, $perPage)->values();

        $classes = SchoolClass::forCurrentYear()->orderBy('niveau')->get();

        return view('dashboard.impayes', compact('impayes', 'classes', 'total', 'year'));
    }

    // -------------------------------------------------------------------------
    // Méthodes privées — construction des statistiques
    // -------------------------------------------------------------------------

    private function buildStats(?AcademicYear $year): array
    {
        if (! $year) {
            return [
                'total_eleves'       => 0,
                'total_classes'      => 0,
                'total_attendu'      => 0,
                'total_collecte'     => 0,
                'total_restant'      => 0,
                'taux_recouvrement'  => 0,
                'nb_impayes'         => 0,
                'evolution_mensuelle'=> [],
            ];
        }

        $totalEleves  = Enrollment::forCurrentYear()->actifs()->count();
        $totalClasses = SchoolClass::forCurrentYear()->count();

        // Total frais attendus = somme (frais_classe × nb_élèves_actifs par classe)
        $totalAttendu = DB::table('school_classes as c')
            ->join('enrollments as e', function ($j) use ($year) {
                $j->on('e.class_id', '=', 'c.id')
                  ->where('e.statut', 'actif')
                  ->where('e.academic_year_id', $year->id);
            })
            ->where('c.academic_year_id', $year->id)
            ->selectRaw('SUM(c.frais_scolarite_annuel) as total')
            ->value('total') ?? 0;

        // Total collecté (paiements valides de l'année)
        $totalCollecte = Payment::valides()
            ->forCurrentYear()
            ->sum('montant_verse');

        $totalRestant = max(0, $totalAttendu - $totalCollecte);
        $tauxRecouvrement = $totalAttendu > 0
            ? round(($totalCollecte / $totalAttendu) * 100, 1)
            : 0;

        // Nombre d'élèves avec impayés
        $nbImpayes = Enrollment::forCurrentYear()
            ->actifs()
            ->get()
            ->filter(fn($e) => $e->reste > 0)
            ->count();

        // Évolution des encaissements par mois (12 derniers mois)
        $evolutionMensuelle = Payment::valides()
            ->forCurrentYear()
            ->selectRaw("DATE_FORMAT(date_paiement, '%Y-%m') as mois, SUM(montant_verse) as total")
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois')
            ->toArray();

        return compact(
            'totalEleves',
            'totalClasses',
            'totalAttendu',
            'totalCollecte',
            'totalRestant',
            'tauxRecouvrement',
            'nbImpayes',
            'evolutionMensuelle'
        );
    }

    private function getImpayes(?AcademicYear $year, int $limit = 10): \Illuminate\Support\Collection
    {
        if (! $year) return collect();

        return Enrollment::with(['student', 'schoolClass'])
            ->forCurrentYear()
            ->actifs()
            ->get()
            ->filter(fn($e) => $e->reste > 0)
            ->sortByDesc('reste')
            ->take($limit)
            ->values();
    }

    private function getStatsParClasse(?AcademicYear $year): \Illuminate\Support\Collection
    {
        if (! $year) return collect();

        return SchoolClass::forCurrentYear()
            ->withCount(['enrollments as nb_eleves' => fn($q) => $q->actifs()])
            ->get()
            ->map(fn($c) => [
                'nom'            => $c->nom,
                'niveau'         => $c->niveau,
                'nb_eleves'      => $c->nb_eleves,
                'total_attendu'  => $c->total_attendu,
                'total_collecte' => $c->total_collecte,
                'total_restant'  => $c->total_restant,
            ]);
    }
}

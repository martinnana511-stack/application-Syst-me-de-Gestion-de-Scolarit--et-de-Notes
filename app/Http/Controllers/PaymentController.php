<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PaymentController extends Controller
{

    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    // -------------------------------------------------------------------------
    // Liste des paiements
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $payments = Payment::with(['enrollment.student', 'enrollment.schoolClass', 'createdBy'])
            ->valides()
            ->forCurrentYear()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('enrollment.student', fn($sq) => $sq->search($request->search));
            })
            ->when($request->filled('classe'), function ($q) use ($request) {
                $q->whereHas('enrollment', fn($sq) => $sq->where('class_id', $request->classe));
            })
            ->when($request->filled('mode'), fn($q) => $q->where('mode_paiement', $request->mode))
            ->when($request->filled('date_from'), fn($q) => $q->where('date_paiement', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->where('date_paiement', '<=', $request->date_to))
            ->latest('date_paiement')
            ->paginate(25)
            ->withQueryString();

        $totalFiltre = $payments->sum('montant_verse');
        $classes     = SchoolClass::forCurrentYear()->orderBy('niveau')->get();

        return view('payments.index', compact('payments', 'classes', 'totalFiltre'));
    }

    // -------------------------------------------------------------------------
    // Formulaire de paiement
    // -------------------------------------------------------------------------

    public function create(Request $request): View
    {
        // Pré-sélection d'un élève si passé en querystring (?student_id=X)
        $student    = $request->filled('student_id')
            ? Student::with('currentEnrollment.schoolClass')->find($request->student_id)
           : null;

        $classes = SchoolClass::forCurrentYear()->orderBy('niveau')->get();

        return view('payments.create', compact('student', 'classes'));
    }

    // -------------------------------------------------------------------------
    // Enregistrement d'un paiement
    // -------------------------------------------------------------------------

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enrollment_id'      => 'required|exists:enrollments,id',
            'type_paiement'      => 'required|in:inscription,scolarite,autre',
            'montant_verse'      => 'required|numeric|min:100',
            'mode_paiement'      => 'required|in:especes,cheque,mobile_money,virement',
            'reference_externe'  => 'nullable|string|max:100',
            'date_paiement'      => 'required|date|before_or_equal:today',
            'observations'       => 'nullable|string|max:500',
        ]);

        $enrollment = Enrollment::with('schoolClass')->findOrFail($data['enrollment_id']);

        // Vérification : ne pas encaisser plus que le montant dû
        $resteAvant = $enrollment->reste;
        if ($data['montant_verse'] > $resteAvant + 0.01) {
            return back()
                ->withInput()
                ->withErrors(['montant_verse' => "Le montant versé ({$data['montant_verse']} F) dépasse le reste à payer ({$resteAvant} F)."]);
        }

        $payment = $this->paymentService->enregistrer($enrollment, $data, auth()->user());

        return redirect()
            ->route('payments.receipt', $payment)
            ->with('success', "Paiement enregistré. Reçu n° {$payment->numero_recu}");
    }

    // -------------------------------------------------------------------------
    // Détail d'un paiement
    // -------------------------------------------------------------------------

    public function show(Payment $payment): View
    {
        $payment->load('enrollment.student', 'enrollment.schoolClass', 'createdBy');
        return view('payments.show', compact('payment'));
    }

    // -------------------------------------------------------------------------
    // Reçu HTML (aperçu)
    // -------------------------------------------------------------------------

    public function receipt(Payment $payment): View
    {
        $this->authorize('generatePdf', $payment);
        $payment->load('enrollment.student', 'enrollment.schoolClass', 'createdBy');
        $historique = Payment::where('enrollment_id', $payment->enrollment_id)
            ->valides()
            ->orderBy('date_paiement')
            ->get();

        return view('payments.receipt', compact('payment', 'historique'));
    }

    // -------------------------------------------------------------------------
    // Génération du reçu PDF
    // -------------------------------------------------------------------------

    public function pdf(Payment $payment): Response
    {
        $this->authorize('generatePdf', $payment);
        $payment->load('enrollment.student', 'enrollment.schoolClass', 'createdBy');

        $pdf = $this->paymentService->genererPdf($payment);

        $filename = "recu_{$payment->numero_recu}.pdf";

        return $pdf->download($filename);
    }

    // -------------------------------------------------------------------------
    // Annulation d'un paiement
    // -------------------------------------------------------------------------

    public function cancel(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorize('cancel', $payment);

        $request->validate([
            'motif_annulation' => 'required|string|max:255',
        ]);

        $payment->update([
            'is_annule'        => true,
            'motif_annulation' => $request->motif_annulation,
        ]);

        return back()->with('success', "Le paiement n° {$payment->numero_recu} a été annulé.");
    }

    // -------------------------------------------------------------------------
    // Historique des paiements d'un élève
    // -------------------------------------------------------------------------

    public function studentPayments(Student $student): View
    {
        $student->load('currentEnrollment.schoolClass');

        $payments = Payment::whereHas('enrollment', fn($q) => $q->where('student_id', $student->id))
            ->with('createdBy')
            ->orderByDesc('date_paiement')
            ->get();

        $fraisTotal  = $student->currentEnrollment?->schoolClass?->frais_scolarite_annuel ?? 0;
        $totalPaye   = $payments->where('is_annule', false)->sum('montant_verse');
        $resteAPayer = max(0, $fraisTotal - $totalPaye);

        return view('payments.student', compact('student', 'payments', 'fraisTotal', 'totalPaye', 'resteAPayer'));
    }
}

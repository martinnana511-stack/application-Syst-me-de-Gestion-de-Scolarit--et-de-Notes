<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    /**
     * Enregistre un paiement, calcule le montant restant et génère le reçu PDF.
     */
    public function enregistrer(Enrollment $enrollment, array $data, User $caissier): Payment
    {
        $frais        = $enrollment->schoolClass->frais_scolarite_annuel;
        $dejaVerse    = Payment::where('enrollment_id', $enrollment->id)
                                ->where('is_annule', false)
                                ->sum('montant_verse');
        $montantDu    = $frais;
        $montantRestant = max(0, $frais - $dejaVerse - $data['montant_verse']);

        $payment = Payment::create([
            'enrollment_id'     => $enrollment->id,
            'type_paiement'     => $data['type_paiement'],
            'montant_verse'     => $data['montant_verse'],
            'montant_du'        => $montantDu,
            'montant_restant'   => $montantRestant,
            'mode_paiement'     => $data['mode_paiement'],
            'reference_externe' => $data['reference_externe'] ?? null,
            'date_paiement'     => $data['date_paiement'],
            'observations'      => $data['observations'] ?? null,
            'created_by'        => $caissier->id,
        ]);

        // Génération automatique du PDF et stockage
        $pdfPath = $this->sauvegarderPdf($payment->fresh()->load('enrollment.student', 'enrollment.schoolClass', 'createdBy'));
        $payment->update(['pdf_path' => $pdfPath]);

        return $payment;
    }

    /**
     * Génère l'instance DomPDF du reçu (pour téléchargement direct).
     */
    public function genererPdf(Payment $payment): \Barryvdh\DomPDF\PDF
    {
        $historique = Payment::where('enrollment_id', $payment->enrollment_id)
            ->valides()
            ->orderBy('date_paiement')
            ->get();

        return Pdf::loadView('payments.pdf-receipt', compact('payment', 'historique'))
            ->setPaper([0, 0, 226.77, 311.81], 'portrait') // Format A5 pour le reçu
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
    }

    /**
     * Sauvegarde le PDF du reçu dans le storage et retourne le chemin.
     */
    private function sauvegarderPdf(Payment $payment): string
    {
        $pdf  = $this->genererPdf($payment);
        $path = "receipts/{$payment->numero_recu}.pdf";
        Storage::disk('public')->put($path, $pdf->output());
        return $path;
    }
}

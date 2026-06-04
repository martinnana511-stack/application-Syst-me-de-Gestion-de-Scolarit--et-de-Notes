{{-- ═══════════════════════════════════════════════════════════
     resources/views/payments/receipt.blade.php  (aperçu HTML)
════════════════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('title', 'Reçu ' . $payment->numero_recu)
@section('page-title', 'Reçu de paiement')
@section('breadcrumb')
    / <a href="{{ route('payments.index') }}">Paiements</a> / {{ $payment->numero_recu }}
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">

        {{-- Actions --}}
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('payments.pdf', $payment) }}" class="btn btn-primary btn-sm" target="_blank">
                <i class="bi bi-file-pdf me-1"></i>Télécharger PDF
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer me-1"></i>Imprimer
            </button>
            <a href="{{ route('payments.student', $payment->enrollment->student) }}"
               class="btn btn-outline-primary btn-sm ms-auto">
                <i class="bi bi-clock-history me-1"></i>Historique de l'élève
            </a>
        </div>

        {{-- Reçu --}}
        <div class="card" id="receipt-card">
            <div class="card-body p-4">

                {{-- En-tête école --}}
                <div class="text-center border-bottom pb-3 mb-3">
                    <div style="font-size:2rem;">🏫</div>
                    <h4 class="fw-bold mb-0" style="font-family:var(--font-heading);color:var(--sgs-primary);">
                        École Primaire
                    </h4>
                    <p class="text-muted small mb-0">Système de Gestion de Scolarité</p>
                    <div class="mt-2">
                        <span class="badge" style="background:var(--sgs-primary);color:#fff;font-size:.9rem;padding:.4rem 1.2rem;border-radius:999px;">
                            REÇU DE PAIEMENT
                        </span>
                    </div>
                </div>

                {{-- Numéro + Date --}}
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <span class="text-muted small">N° de reçu</span>
                        <div class="fw-bold" style="font-family:var(--font-heading);font-size:1.1rem;color:var(--sgs-primary);">
                            {{ $payment->numero_recu }}
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small">Date</span>
                        <div class="fw-bold">{{ $payment->date_paiement->format('d/m/Y') }}</div>
                    </div>
                </div>

                {{-- Informations élève --}}
                <div class="rounded p-3 mb-3" style="background:#f4f6f9;">
                    <p class="small fw-semibold text-muted mb-2 text-uppercase">Élève</p>
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $payment->enrollment->student->photo_url }}"
                             class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                        <div>
                            <div class="fw-bold">{{ $payment->enrollment->student->nom_complet }}</div>
                            <div class="text-muted small">
                                {{ $payment->enrollment->student->matricule }}
                                · {{ $payment->enrollment->schoolClass->nom }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Détail du paiement --}}
                <table class="table table-sm mb-3">
                    <tbody>
                        <tr>
                            <td class="text-muted small">Type de paiement</td>
                            <td class="fw-semibold text-end">{{ $payment->type_label }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Mode de paiement</td>
                            <td class="text-end">{{ $payment->mode_label }}</td>
                        </tr>
                        @if($payment->reference_externe)
                        <tr>
                            <td class="text-muted small">Référence</td>
                            <td class="text-end"><code>{{ $payment->reference_externe }}</code></td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted small">Frais total annuel</td>
                            <td class="text-end">{{ number_format($payment->montant_du, 0, ',', ' ') }} F CFA</td>
                        </tr>
                        <tr class="table-success">
                            <td class="fw-bold">Montant versé</td>
                            <td class="fw-bold text-success text-end fs-5">
                                {{ number_format($payment->montant_verse, 0, ',', ' ') }} F CFA
                            </td>
                        </tr>
                        <tr class="{{ $payment->montant_restant > 0 ? 'table-warning' : 'table-success' }}">
                            <td class="fw-bold">Reste à payer</td>
                            <td class="fw-bold text-end {{ $payment->montant_restant > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($payment->montant_restant, 0, ',', ' ') }} F CFA
                                @if($payment->montant_restant == 0)
                                    <i class="bi bi-check-circle-fill ms-1"></i>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- Historique des versements --}}
                @if($historique->count() > 1)
                <div class="border-top pt-3">
                    <p class="small fw-semibold text-muted mb-2">HISTORIQUE DES VERSEMENTS</p>
                    <table class="table table-sm">
                        <thead><tr><th>Date</th><th>N° Reçu</th><th class="text-end">Montant</th></tr></thead>
                        <tbody>
                            @foreach($historique as $h)
                            <tr class="{{ $h->id === $payment->id ? 'fw-bold' : '' }}">
                                <td class="small">{{ $h->date_paiement->format('d/m/Y') }}</td>
                                <td class="small"><code>{{ $h->numero_recu }}</code></td>
                                <td class="text-end small">{{ number_format($h->montant_verse, 0, ',', ' ') }} F</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- Pied de reçu --}}
                <div class="border-top pt-3 mt-2 d-flex justify-content-between align-items-end">
                    <div class="text-muted" style="font-size:.72rem;">
                        Reçu émis le {{ now()->format('d/m/Y à H:i') }}<br>
                        Par : {{ $payment->createdBy?->name ?? '—' }}
                    </div>
                    <div class="text-end">
                        <div class="text-muted small mb-1">Signature / cachet</div>
                        <div style="width:100px;height:40px;border-bottom:2px solid #ccc;"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
@media print {
    #sidebar, #topbar, .btn, .page-body > .d-flex { display: none !important; }
    #main-content { margin: 0; padding: 0; }
    #receipt-card { border: none; box-shadow: none; }
}
</style>
@endpush

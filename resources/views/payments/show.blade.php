@extends('layouts.app')
@section('title', 'Paiement — ' . $payment->numero_recu)
@section('page-title', 'Détail du paiement')
@section('breadcrumb')
    / <a href="{{ route('payments.index') }}">Paiements</a>
    / {{ $payment->numero_recu }}
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">

    {{-- Actions --}}
    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
        <a href="{{ route('payments.receipt', $payment) }}"
           class="btn btn-outline-primary btn-sm">
            <i class="bi bi-eye me-1"></i>Voir reçu
        </a>
        <a href="{{ route('payments.pdf', $payment) }}"
           class="btn btn-primary btn-sm" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Télécharger PDF
        </a>
        @if(!$payment->is_annule)
        <button class="btn btn-outline-danger btn-sm ms-auto"
                data-bs-toggle="modal" data-bs-target="#modalAnnuler">
            <i class="bi bi-x-circle me-1"></i>Annuler ce paiement
        </button>
        @endif
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="bi bi-receipt me-2"></i>
                <strong>{{ $payment->numero_recu }}</strong>
            </span>
            @if($payment->is_annule)
                <span class="badge bg-danger">Annulé</span>
            @else
                <span class="badge bg-success">Valide</span>
            @endif
        </div>
        <div class="card-body">

            {{-- Infos élève --}}
            <div class="p-3 rounded mb-4" style="background:#f4f6f9;">
                <p class="small fw-semibold text-muted text-uppercase mb-2">Élève</p>
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $payment->enrollment->student->photo_url }}"
                         class="rounded-circle"
                         style="width:52px;height:52px;object-fit:cover;border:2px solid #e4e8ed;">
                    <div>
                        <div class="fw-bold fs-6">
                            {{ $payment->enrollment->student->nom_complet }}
                        </div>
                        <div class="text-muted small">
                            <span class="me-2">
                                <i class="bi bi-tag me-1"></i>
                                {{ $payment->enrollment->student->matricule }}
                            </span>
                            <span class="badge" style="background:#eaf2ff;color:var(--sgs-primary);">
                                {{ $payment->enrollment->schoolClass->nom }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Détails en deux colonnes --}}
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-semibold text-muted text-uppercase small mb-3">
                        Informations du paiement
                    </h6>
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td class="text-muted small" style="width:45%;">Date</td>
                                <td class="fw-semibold">
                                    {{ $payment->date_paiement->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Type</td>
                                <td>{{ $payment->type_label }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Mode</td>
                                <td>{{ $payment->mode_label }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Référence externe</td>
                                <td>
                                    @if($payment->reference_externe)
                                        <code class="small">{{ $payment->reference_externe }}</code>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Encaissé par</td>
                                <td>{{ $payment->createdBy?->name ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="fw-semibold text-muted text-uppercase small mb-3">
                        Situation financière
                    </h6>
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td class="text-muted small" style="width:50%;">Total dû</td>
                                <td class="fw-semibold">
                                    {{ number_format($payment->montant_du, 0, ',', ' ') }} F
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Montant versé</td>
                                <td class="fw-bold text-success fs-6">
                                    {{ number_format($payment->montant_verse, 0, ',', ' ') }} F
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Reste à payer</td>
                                <td class="fw-bold {{ $payment->montant_restant > 0 ? 'text-danger' : 'text-success' }} fs-6">
                                    {{ number_format($payment->montant_restant, 0, ',', ' ') }} F
                                    @if($payment->montant_restant == 0)
                                        <i class="bi bi-check-circle-fill ms-1"></i>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Barre de progression --}}
                    @php
                        $pct = $payment->montant_du > 0
                            ? min(100, round(($payment->montant_du - $payment->montant_restant) / $payment->montant_du * 100))
                            : 100;
                        $color = $pct >= 100 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
                    @endphp
                    <div class="progress mt-2" style="height:8px;">
                        <div class="progress-bar bg-{{ $color }}"
                             style="width:{{ $pct }}%;">
                        </div>
                    </div>
                    <div class="text-end mt-1 small text-muted">{{ $pct }}% réglé</div>
                </div>
            </div>

            {{-- Observations --}}
            @if($payment->observations)
            <div class="mt-3 p-3 rounded" style="background:#fef9e7;border-left:3px solid #e67e22;">
                <p class="small fw-semibold mb-1">
                    <i class="bi bi-chat-text me-1"></i>Observations
                </p>
                <p class="small mb-0 text-muted">{{ $payment->observations }}</p>
            </div>
            @endif

            {{-- Motif annulation --}}
            @if($payment->is_annule && $payment->motif_annulation)
            <div class="mt-3 p-3 rounded" style="background:#fdedec;border-left:3px solid #e74c3c;">
                <p class="small fw-semibold mb-1 text-danger">
                    <i class="bi bi-x-circle me-1"></i>Motif d'annulation
                </p>
                <p class="small mb-0 text-muted">{{ $payment->motif_annulation }}</p>
            </div>
            @endif

        </div>
    </div>

</div>
</div>

{{-- Modal annulation --}}
@if(!$payment->is_annule)
<div class="modal fade" id="modalAnnuler" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-x-circle me-2"></i>Annuler le paiement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('payments.cancel', $payment) }}">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Cette action est irréversible. Le paiement
                        <strong>{{ $payment->numero_recu }}</strong> sera marqué comme annulé.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium small">
                            Motif d'annulation <span class="text-danger">*</span>
                        </label>
                        <textarea name="motif_annulation"
                                  class="form-control form-control-sm"
                                  rows="3"
                                  placeholder="Expliquez la raison de l'annulation..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm"
                            data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
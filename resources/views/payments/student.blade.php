@extends('layouts.app')
@section('title', 'Paiements — ' . $student->nom_complet)
@section('page-title', 'Historique des paiements')
@section('breadcrumb')
    / <a href="{{ route('students.index') }}">Élèves</a>
    / <a href="{{ route('students.show', $student) }}">{{ $student->nom_complet }}</a>
    / Paiements
@endsection

@section('content')

{{-- Résumé financier --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eaf2ff;">💰</div>
            <div>
                <div class="stat-value">{{ number_format($fraisTotal, 0, ',', ' ') }} F</div>
                <div class="stat-label">Total dû</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eafaf1;">✅</div>
            <div>
                <div class="stat-value text-success">{{ number_format($totalPaye, 0, ',', ' ') }} F</div>
                <div class="stat-label">Total payé</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdedec;">⚠️</div>
            <div>
                <div class="stat-value {{ $resteAPayer > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($resteAPayer, 0, ',', ' ') }} F
                </div>
                <div class="stat-label">Reste à payer</div>
            </div>
        </div>
    </div>
</div>

{{-- Infos élève + bouton paiement --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <img src="{{ $student->photo_url }}" class="rounded-circle"
             style="width:42px;height:42px;object-fit:cover;">
        <div>
            <div class="fw-bold">{{ $student->nom_complet }}</div>
            <div class="text-muted small">
                {{ $student->matricule }}
                @if($student->currentEnrollment?->schoolClass)
                    · {{ $student->currentEnrollment->schoolClass->nom }}
                @endif
            </div>
        </div>
    </div>
    @if($resteAPayer > 0)
    <a href="{{ route('payments.create', ['student_id' => $student->id]) }}"
       class="btn btn-primary btn-sm">
        <i class="bi bi-cash-coin me-1"></i>Nouveau paiement
    </a>
    @endif
</div>

{{-- Tableau des paiements --}}
<div class="card">
    <div class="card-header">
        <i class="bi bi-clock-history me-2"></i>Historique des versements
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>N° Reçu</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Mode</th>
                        <th>Montant versé</th>
                        <th>Reste après</th>
                        <th>Encaissé par</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr class="{{ $payment->is_annule ? 'opacity-50' : '' }}">
                        <td><code class="small">{{ $payment->numero_recu }}</code></td>
                        <td class="small">{{ $payment->date_paiement->format('d/m/Y') }}</td>
                        <td class="small text-muted">{{ $payment->type_label }}</td>
                        <td class="small">{{ $payment->mode_label }}</td>
                        <td class="fw-semibold {{ $payment->is_annule ? 'text-muted' : 'text-success' }}">
                            {{ number_format($payment->montant_verse, 0, ',', ' ') }} F
                        </td>
                        <td class="small {{ $payment->montant_restant > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($payment->montant_restant, 0, ',', ' ') }} F
                        </td>
                        <td class="small text-muted">{{ $payment->createdBy?->name ?? '—' }}</td>
                        <td>
                            @if($payment->is_annule)
                                <span class="badge bg-danger-subtle text-danger">Annulé</span>
                            @else
                                <span class="badge bg-success-subtle text-success">Valide</span>
                            @endif
                        </td>
                        <td>
                            @if(!$payment->is_annule)
                            <div class="d-flex gap-1">
                                <a href="{{ route('payments.receipt', $payment) }}"
                                   class="btn btn-xs btn-outline-primary" title="Voir reçu">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('payments.pdf', $payment) }}"
                                   class="btn btn-xs btn-outline-secondary" title="PDF" target="_blank">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-cash-coin fs-2 d-block mb-2 opacity-25"></i>
                            Aucun paiement enregistré.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>.btn-xs { padding:.2rem .45rem; font-size:.75rem; }</style>
@endpush
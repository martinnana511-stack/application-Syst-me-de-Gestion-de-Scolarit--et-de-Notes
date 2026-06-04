@extends('layouts.app')
@section('title', 'Paiements')
@section('page-title', 'Gestion des paiements')
@section('breadcrumb') / <a href="#">Paiements</a>@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <p class="text-muted mb-0 small">
        <strong>{{ $payments->total() }}</strong> paiement(s) trouvé(s)
        — Total : <strong class="text-success">{{ number_format($totalFiltre, 0, ',', ' ') }} F CFA</strong>
    </p>
    <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Nouveau paiement
    </a>
</div>

{{-- Filtres --}}
<div class="card mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="🔍 Rechercher un élève…" value="{{ request('search') }}">
            </div>
            <div class="col-sm-2">
                <select name="classe" class="form-select form-select-sm">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected(request('classe') == $c->id)>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <select name="mode" class="form-select form-select-sm">
                    <option value="">Tous les modes</option>
                    <option value="especes"      @selected(request('mode') == 'especes')>Espèces</option>
                    <option value="mobile_money" @selected(request('mode') == 'mobile_money')>Mobile Money</option>
                    <option value="cheque"       @selected(request('mode') == 'cheque')>Chèque</option>
                    <option value="virement"     @selected(request('mode') == 'virement')>Virement</option>
                </select>
            </div>
            <div class="col-sm-2">
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ request('date_from') }}" placeholder="Du">
            </div>
            <div class="col-sm-2">
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ request('date_to') }}" placeholder="Au">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm">Filtrer</button>
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

{{-- Tableau --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>N° Reçu</th>
                        <th>Élève</th>
                        <th>Classe</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Mode</th>
                        <th>Montant versé</th>
                        <th>Reste</th>
                        <th>Encaissé par</th>
                        <th>Détails</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td><code class="small">{{ $payment->numero_recu }}</code></td>
                        <td>
                            <a href="{{ route('students.show', $payment->enrollment->student) }}"
                               class="fw-semibold text-dark text-decoration-none small">
                                {{ $payment->enrollment->student->nom_complet }}
                            </a>
                        </td>
                        <td>
                            <span class="badge" style="background:#eaf2ff;color:var(--sgs-primary);font-size:.72rem;">
                                {{ $payment->enrollment->schoolClass->nom }}
                            </span>
                        </td>
                        <td class="small">{{ $payment->date_paiement->format('d/m/Y') }}</td>
                        <td class="small text-muted">{{ $payment->type_label }}</td>
                        <td class="small">{{ $payment->mode_label }}</td>
                        <td class="fw-semibold text-success">
                            {{ number_format($payment->montant_verse, 0, ',', ' ') }} F
                        </td>
                        <td class="{{ $payment->montant_restant > 0 ? 'text-danger' : 'text-success' }} small fw-semibold">
                            {{ number_format($payment->montant_restant, 0, ',', ' ') }} F
                            @if($payment->montant_restant == 0)
                                <i class="bi bi-check-circle-fill"></i>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $payment->createdBy?->name ?? '—' }}</td>
                        <td class="text-center">
                            @if($payment->reference_externe || $payment->observations)
                                <a href="{{ route('payments.show', $payment) }}">
                                    <i class="bi bi-info-circle text-primary"></i>
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-cash-coin fs-2 d-block mb-2 opacity-25"></i>
                            Aucun paiement trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center px-3 py-2">
        <span class="text-muted small">Page {{ $payments->currentPage() }} / {{ $payments->lastPage() }}</span>
        {{ $payments->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>.btn-xs { padding:.2rem .45rem; font-size:.75rem; }</style>
@endpush
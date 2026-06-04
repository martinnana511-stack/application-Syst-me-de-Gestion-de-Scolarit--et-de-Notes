{{-- ════════════════════════════════════════════════════════════
     resources/views/dashboard/impayes.blade.php
═════════════════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('title', 'Impayés')
@section('page-title', 'Élèves en retard de paiement')
@section('breadcrumb')
    / <a href="{{ route('dashboard.index') }}">Tableau de bord</a> / Impayés
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <p class="text-muted mb-0 small">
        <strong class="text-danger">{{ $total }}</strong> élève(s) avec des impayés
    </p>
</div>

<div class="card mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="🔍 Rechercher un élève…" value="{{ request('search') }}">
            </div>
            <div class="col-sm-3">
                <select name="classe" class="form-select form-select-sm">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected(request('classe') == $c->id)>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
                <a href="{{ route('dashboard.impayes') }}" class="btn btn-outline-secondary btn-sm ms-1">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Élève</th>
                        <th>Classe</th>
                        <th>Total dû</th>
                        <th>Payé</th>
                        <th>Restant</th>
                        <th>Progression</th>
                        <th>Tuteur</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($impayes as $e)
                    @php
                        $frais = $e->schoolClass->frais_scolarite_annuel;
                        $paye  = $e->montant_paye;
                        $reste = $e->reste;
                        $pct   = $frais > 0 ? round($paye / $frais * 100) : 0;
                        $color = $pct >= 50 ? 'warning' : 'danger';
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $e->student->photo_url }}" class="rounded-circle"
                                     style="width:32px;height:32px;object-fit:cover;">
                                <div>
                                    <div class="fw-semibold small">{{ $e->student->nom_complet }}</div>
                                    <div style="font-size:.7rem;color:#aaa;">{{ $e->student->matricule }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-niveau" style="background:#eaf2ff;color:var(--sgs-primary);">
                                {{ $e->schoolClass->nom }}
                            </span>
                        </td>
                        <td class="small">{{ number_format($frais, 0, ',', ' ') }} F</td>
                        <td class="small text-success fw-semibold">{{ number_format($paye, 0, ',', ' ') }} F</td>
                        <td class="fw-bold text-danger">{{ number_format($reste, 0, ',', ' ') }} F</td>
                        <td style="min-width:100px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px;">
                                    <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%;"></div>
                                </div>
                                <span class="text-muted" style="font-size:.72rem;">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td class="small text-muted">
                            {{ $e->student->tuteur_nom ?? $e->student->nom_pere ?? '—' }}
                            @if($e->student->tuteur_telephone)
                                <br><a href="tel:{{ $e->student->tuteur_telephone }}" class="text-dark">
                                    {{ $e->student->tuteur_telephone }}
                                </a>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('students.show', $e->student) }}"
                                   class="btn btn-xs btn-outline-primary" title="Fiche">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('payments.create', ['student_id' => $e->student_id]) }}"
                                   class="btn btn-xs btn-accent" title="Encaisser">
                                    <i class="bi bi-cash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle-fill fs-2 text-success d-block mb-2"></i>
                            Aucun impayé trouvé avec ces critères.
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
<style>
.btn-xs { padding:.2rem .45rem; font-size:.75rem; }
.btn-accent { background:var(--sgs-accent); border-color:var(--sgs-accent); color:#fff; }
.btn-accent:hover { background:#ca6f1e; color:#fff; }
</style>
@endpush
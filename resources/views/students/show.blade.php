@extends('layouts.app')
@section('title', $student->nom_complet)
@section('page-title', 'Fiche élève')
@section('breadcrumb')
    / <a href="{{ route('students.index') }}">Élèves</a> / {{ $student->nom_complet }}
@endsection

@section('content')

<div class="row g-3">

    {{-- ── Carte identité ──────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card text-center mb-3">
            <div class="card-body py-4">
                <img src="{{ $student->photo_url }}"
                     class="rounded-circle object-fit-cover mb-3"
                     style="width:90px;height:90px;border:3px solid #e4e8ed;">
                <h5 class="fw-bold mb-0" style="font-family:var(--font-heading);">
                    {{ $student->nom_complet }}
                </h5>
                <code class="text-muted small">{{ $student->matricule }}</code>
                <div class="mt-2">
                    @if($classe = $student->currentEnrollment?->schoolClass)
                        <span class="badge" style="background:#eaf2ff;color:var(--sgs-primary);font-size:.8rem;">
                            {{ $classe->nom }}
                        </span>
                    @endif
                    <span class="badge ms-1"
                          style="background:{{ $student->sexe === 'M' ? '#eaf2ff' : '#fdf2f8' }};
                                 color:{{ $student->sexe === 'M' ? 'var(--sgs-primary)' : '#8e44ad' }};">
                        {{ $student->sexe === 'M' ? '♂' : '♀' }} {{ $student->sexe === 'M' ? 'Masculin' : 'Féminin' }}
                    </span>
                </div>
                <div class="mt-3 text-muted small">
                    <i class="bi bi-cake me-1"></i>{{ $student->date_naissance->format('d/m/Y') }}
                    ({{ $student->age }} ans)
                </div>
            </div>
            <div class="card-footer bg-white d-flex gap-2 justify-content-center pb-3">
                <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Modifier
                </a>
                <a href="{{ route('payments.create', ['student_id' => $student->id]) }}"
                   class="btn btn-sm btn-accent">
                    <i class="bi bi-cash me-1"></i>Paiement
                </a>
            </div>
        </div>

        {{-- Infos famille --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-house me-2"></i>Famille</div>
            <div class="card-body small">
                @if($student->nom_pere)
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Père</span>
                    <strong>{{ $student->nom_pere }}</strong>
                </div>
                @endif
                @if($student->nom_mere)
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Mère</span>
                    <strong>{{ $student->nom_mere }}</strong>
                </div>
                @endif
                @if($student->tuteur_nom)
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Tuteur</span>
                    <strong>{{ $student->tuteur_nom }}</strong>
                </div>
                @endif
                @if($student->tuteur_telephone)
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Téléphone</span>
                    <strong>{{ $student->tuteur_telephone }}</strong>
                </div>
                @endif
                @if($student->adresse)
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Adresse</span>
                    <strong>{{ $student->adresse }}</strong>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Colonne droite ───────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Situation financière --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash-coin me-2"></i>Situation financière</span>
                <a href="{{ route('payments.student', $student) }}" class="btn btn-sm btn-outline-primary">
                    Historique complet
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-4 text-center">
                        <div class="fw-bold" style="font-size:1.2rem;color:var(--sgs-primary);">
                            {{ number_format($fraisTotal, 0, ',', ' ') }} F
                        </div>
                        <div class="text-muted" style="font-size:.75rem;">Total dû</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fw-bold text-success" style="font-size:1.2rem;">
                            {{ number_format($totalPaye, 0, ',', ' ') }} F
                        </div>
                        <div class="text-muted" style="font-size:.75rem;">Payé</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fw-bold {{ $resteAPayer > 0 ? 'text-danger' : 'text-success' }}" style="font-size:1.2rem;">
                            {{ number_format($resteAPayer, 0, ',', ' ') }} F
                        </div>
                        <div class="text-muted" style="font-size:.75rem;">Restant</div>
                    </div>
                </div>
                @php $pct = $fraisTotal > 0 ? min(100, round($totalPaye / $fraisTotal * 100)) : 100; @endphp
                <div class="progress" style="height:10px;">
                    <div class="progress-bar {{ $resteAPayer > 0 ? 'bg-warning' : 'bg-success' }}"
                         style="width:{{ $pct }}%;" role="progressbar">
                    </div>
                </div>
                <div class="text-end mt-1 small text-muted">{{ $pct }}% réglé</div>

                {{-- Derniers paiements --}}
                @if($student->payments->count())
                <hr class="my-2">
                <p class="small fw-semibold mb-2">Derniers versements</p>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>N° Reçu</th>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Mode</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->payments->take(5) as $p)
                            <tr>
                                <td><code class="small">{{ $p->numero_recu }}</code></td>
                                <td class="small">{{ $p->date_paiement->format('d/m/Y') }}</td>
                                <td class="fw-semibold text-success">{{ number_format($p->montant_verse, 0, ',', ' ') }} F</td>
                                <td class="small text-muted">{{ $p->mode_label }}</td>
                                <td>
                                    <a href="{{ route('payments.pdf', $p) }}"
                                       class="btn btn-xs btn-outline-secondary" title="Reçu PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- Historique des inscriptions --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Historique scolaire</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Année</th>
                            <th>Classe</th>
                            <th>Inscription</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->enrollments->sortByDesc('date_inscription') as $e)
                        <tr>
                            <td class="small">{{ $e->academicYear->libelle }}</td>
                            <td>
                                <span class="badge badge-niveau" style="background:#eaf2ff;color:var(--sgs-primary);">
                                    {{ $e->schoolClass->nom ?? '—' }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $e->date_inscription->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $colors = ['actif'=>'success','transfere'=>'warning','exclu'=>'danger','diplome'=>'info'];
                                @endphp
                                <span class="badge bg-{{ $colors[$e->statut] ?? 'secondary' }}-subtle
                                             text-{{ $colors[$e->statut] ?? 'secondary' }}">
                                    {{ ucfirst($e->statut) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>.btn-xs { padding:.2rem .45rem; font-size:.75rem; }</style>
@endpush

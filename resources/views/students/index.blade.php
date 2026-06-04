@extends('layouts.app')
@section('title', 'Élèves')
@section('page-title', 'Gestion des élèves')
@section('breadcrumb') / <a href="#">Élèves</a>@endsection

@section('content')

{{-- ── En-tête + actions ────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <p class="text-muted mb-0 small">
        <strong>{{ $students->total() }}</strong> élève(s) trouvé(s)
    </p>
    <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus me-1"></i> Inscrire un élève
    </a>
</div>

{{-- ── Filtres ──────────────────────────────────────── --}}
<div class="card mb-3">
    <div class="card-body py-2 px-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="🔍 Nom, prénom ou matricule…" value="{{ request('search') }}">
            </div>
            <div class="col-sm-3">
                <select name="classe" class="form-select form-select-sm">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected(request('classe') == $c->id)>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <select name="sexe" class="form-select form-select-sm">
                    <option value="">Tout sexe</option>
                    <option value="M" @selected(request('sexe') == 'M')>Masculin</option>
                    <option value="F" @selected(request('sexe') == 'F')>Féminin</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm">Filtrer</button>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

{{-- ── Tableau ───────────────────────────────────────── --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Matricule</th>
                        <th>Nom complet</th>
                        <th>Classe</th>
                        <th>Sexe</th>
                        <th>Tuteur / Tél.</th>
                        <th>Situation</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>
                            <img src="{{ $student->photo_url }}" alt="Photo"
                                 class="rounded-circle object-fit-cover"
                                 style="width:36px;height:36px;border:2px solid #e4e8ed;">
                        </td>
                        <td>
                            <code class="text-muted" style="font-size:.78rem;">{{ $student->matricule }}</code>
                        </td>
                        <td>
                            <a href="{{ route('students.show', $student) }}"
                               class="fw-semibold text-dark text-decoration-none">
                                {{ $student->nom_complet }}
                            </a>
                        </td>
                        <td>
                            @if($classe = $student->currentEnrollment?->schoolClass)
                                <span class="badge badge-niveau"
                                      style="background:#eaf2ff;color:var(--sgs-primary);">
                                    {{ $classe->nom }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background:{{ $student->sexe === 'M' ? '#eaf2ff' : '#fdf2f8' }};
                                  color:{{ $student->sexe === 'M' ? 'var(--sgs-primary)' : '#8e44ad' }};">
                                {{ $student->sexe === 'M' ? '♂ Masculin' : '♀ Féminin' }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            {{ $student->tuteur_nom ?? ($student->nom_pere ?? '—') }}
                            @if($student->tuteur_telephone)
                                <br><span class="text-dark">{{ $student->tuteur_telephone }}</span>
                            @endif
                        </td>
                        <td>
                            @php $reste = $student->resteAPayer(); @endphp
                            @if($reste > 0)
                                <span class="badge bg-danger-subtle text-danger">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ number_format($reste, 0, ',', ' ') }} F
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-check-circle me-1"></i>À jour
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('students.show', $student) }}"
                                   class="btn btn-xs btn-outline-primary" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('students.edit', $student) }}"
                                   class="btn btn-xs btn-outline-secondary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('payments.create', ['student_id' => $student->id]) }}"
                                   class="btn btn-xs btn-outline-success" title="Paiement">
                                    <i class="bi bi-cash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                            Aucun élève trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($students->hasPages())
    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center px-3 py-2">
        <span class="text-muted small">
            Page {{ $students->currentPage() }} / {{ $students->lastPage() }}
        </span>
        {{ $students->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
.btn-xs { padding: .2rem .45rem; font-size: .75rem; }
</style>
@endpush

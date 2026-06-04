@extends('layouts.app')
@section('title', 'Années scolaires')
@section('page-title', 'Années scolaires')
@section('breadcrumb') / Paramètres / Années scolaires@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0 small"><strong>{{ $years->count() }}</strong> année(s)</p>
    <a href="{{ route('settings.academic-years.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Nouvelle année
    </a>
</div>

@foreach($years as $year)
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <span style="font-family:var(--font-heading);font-size:1.1rem;font-weight:700;color:var(--sgs-primary);">
                {{ $year->libelle }}
            </span>
            @if($year->is_active)
                <span class="badge bg-success-subtle text-success">
                    <i class="bi bi-check-circle me-1"></i>Active
                </span>
            @else
                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
            @endif
            <span class="text-muted small">
                {{ $year->date_debut->format('d/m/Y') }} → {{ $year->date_fin->format('d/m/Y') }}
            </span>
        </div>
        <div class="d-flex gap-2">
            @if(!$year->is_active)
            <form method="POST" action="{{ route('settings.academic-years.activate', $year) }}"
                  onsubmit="return confirm('Activer cette année scolaire ?')">
                @csrf @method('PATCH')
                <button class="btn btn-xs btn-outline-success" title="Activer">
                    <i class="bi bi-play me-1"></i>Activer
                </button>
            </form>
            @endif
            <a href="{{ route('settings.academic-years.edit', $year) }}"
               class="btn btn-xs btn-outline-secondary">
                <i class="bi bi-pencil"></i>
            </a>
        </div>
    </div>

    {{-- Trimestres de cette année --}}
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">
                <i class="bi bi-calendar3 me-2 text-primary"></i>Trimestres
            </h6>
            @if($year->terms->count() < 3)
            <a href="{{ route('settings.terms.create', $year) }}"
               class="btn btn-xs btn-outline-primary">
                <i class="bi bi-plus me-1"></i>Ajouter un trimestre
            </a>
            @endif
        </div>

        @if($year->terms->count() === 0)
        <div class="alert alert-warning py-2 small">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Aucun trimestre créé. Les enseignants ne pourront pas saisir de notes.
            <a href="{{ route('settings.terms.create', $year) }}" class="alert-link ms-1">
                Créer les trimestres →
            </a>
        </div>
        @else
        <div class="row g-3">
            @foreach($year->terms->sortBy('numero') as $term)
            <div class="col-md-4">
                <div class="border rounded p-3 {{ $term->is_closed ? 'bg-light' : 'bg-white' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-semibold">{{ $term->libelle }}</div>
                            <div class="text-muted small">
                                {{ $term->date_debut->format('d/m/Y') }}
                                → {{ $term->date_fin->format('d/m/Y') }}
                            </div>
                        </div>
                        @if($term->is_closed)
                            <span class="badge bg-danger-subtle text-danger">
                                <i class="bi bi-lock me-1"></i>Clôturé
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success">
                                <i class="bi bi-unlock me-1"></i>Ouvert
                            </span>
                        @endif
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        @if($term->is_closed)
                        {{-- Réouvrir --}}
                        <form method="POST"
                              action="{{ route('settings.terms.reopen', $term) }}"
                              onsubmit="return confirm('Réouvrir ce trimestre ? Les notes pourront à nouveau être modifiées.')">
                            @csrf @method('PATCH')
                            <button class="btn btn-xs btn-outline-success" title="Réouvrir">
                                <i class="bi bi-unlock me-1"></i>Réouvrir
                            </button>
                        </form>
                        @else
                        {{-- Clôturer --}}
                        <form method="POST"
                              action="{{ route('settings.terms.close', $term) }}"
                              onsubmit="return confirm('Clôturer ce trimestre ? Les notes ne pourront plus être modifiées.')">
                            @csrf @method('PATCH')
                            <button class="btn btn-xs btn-outline-danger" title="Clôturer">
                                <i class="bi bi-lock me-1"></i>Clôturer
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('settings.terms.edit', $term) }}"
                           class="btn btn-xs btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <form method="POST"
                              action="{{ route('settings.terms.destroy', $term) }}"
                              onsubmit="return confirm('Supprimer ce trimestre ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endforeach

@endsection
@push('styles')
<style>.btn-xs { padding:.2rem .45rem; font-size:.75rem; }</style>
@endpush
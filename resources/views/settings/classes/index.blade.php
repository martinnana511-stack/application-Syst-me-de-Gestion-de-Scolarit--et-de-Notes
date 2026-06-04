@extends('layouts.app')
@section('title', 'Classes')
@section('page-title', 'Gestion des classes')
@section('breadcrumb') / <a href="#">Paramètres</a> / Classes@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0 small"><strong>{{ $classes->count() }}</strong> classe(s)</p>
    <a href="{{ route('settings.classes.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Nouvelle classe
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Classe</th>
                        <th>Niveau</th>
                        <th>Enseignant</th>
                        <th>Élèves</th>
                        <th>Capacité max</th>
                        <th>Frais inscription</th>
                        <th>Frais scolarité</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $classe)
                    <tr>
                        <td class="fw-semibold">{{ $classe->nom }}</td>
                        <td>
                            <span class="badge" style="background:#eaf2ff;color:var(--sgs-primary);">
                                {{ $classe->niveau }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $classe->teacher?->name ?? '—' }}</td>
                        <td>
                            <span class="{{ $classe->nb_eleves >= $classe->effectif_max ? 'text-danger fw-bold' : '' }}">
                                {{ $classe->nb_eleves }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $classe->effectif_max }}</td>
                        <td class="small">{{ number_format($classe->frais_inscription, 0, ',', ' ') }} F</td>
                        <td class="small fw-semibold">{{ number_format($classe->frais_scolarite_annuel, 0, ',', ' ') }} F</td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('settings.classes.edit', $classe) }}"
                                   class="btn btn-xs btn-outline-secondary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('settings.classes.destroy', $classe) }}"
                                      onsubmit="return confirm('Supprimer cette classe ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-building fs-2 d-block mb-2 opacity-25"></i>
                            Aucune classe configurée.
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
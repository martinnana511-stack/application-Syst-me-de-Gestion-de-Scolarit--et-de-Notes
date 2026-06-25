@extends('layouts.app')

@section('title', 'Absences')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des Absences</h1>
        <a href="{{ route('absences.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nouvelle absence
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filtres --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('absences.index') }}" class="row g-3">
                <div class="col-md-5">
                    <select name="class_id" class="form-select">
                        <option value="">-- Toutes les classes --</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ request('class_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Rechercher par nom..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Élève</th>
                        <th>Date</th>
                        <th>Motif</th>
                        <th>Justifiée</th>
                        <th>Enregistrée par</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absences as $absence)
                    <tr>
                        <td>{{ $absence->student?->nom }} {{ $absence->student?->prenom }}</td>
                        <td>{{ $absence->date->format('d/m/Y') }}</td>
                        <td>{{ $absence->motif ?? 'Non renseigné' }}</td>
                        <td>
                            @if($absence->justifie)
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="badge bg-danger">Non</span>
                            @endif
                        </td>
                        <td>{{ $absence->createdBy?->name ?? '—' }}</td>
                        <td>
                            <a href="{{ route('absences.edit', $absence) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('absences.destroy', $absence) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Supprimer cette absence ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Aucune absence enregistrée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $absences->links() }}
        </div>
    </div>
</div>
@endsection
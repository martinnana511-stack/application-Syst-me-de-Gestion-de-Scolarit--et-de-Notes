@extends('layouts.app')

@section('title', 'Détail Parent')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Détail du Parent</h1>
        <div>
            <a href="{{ route('parents.edit', $parent) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Modifier
            </a>
            <a href="{{ route('parents.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations du parent -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Nom complet</th>
                            <td>{{ $parent->user?->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $parent->user?->email }}</td>
                        </tr>
                        <tr>
                            <th>Téléphone</th>
                            <td>{{ $parent->user?->telephone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Téléphone urgence</th>
                            <td>{{ $parent->telephone_urgence ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Profession</th>
                            <td>{{ $parent->profession ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Adresse</th>
                            <td>{{ $parent->adresse ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Statut</th>
                            <td>
                                @if($parent->user?->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Élèves liés -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Élèves liés</h5>
                </div>
                <div class="card-body">
                    @forelse($parent->students as $student)
                    <div class="d-flex align-items-center mb-3 p-2 border rounded">
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ $student->nom }} {{ $student->prenom }}</div>
                            <small class="text-muted">
                                Lien : {{ ucfirst($student->pivot->lien_parente) }}
                            </small>
                        </div>
                        <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                    @empty
                    <p class="text-muted">Aucun élève lié à ce parent.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
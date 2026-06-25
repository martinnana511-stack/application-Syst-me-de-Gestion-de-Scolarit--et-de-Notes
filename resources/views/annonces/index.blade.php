@extends('layouts.app')

@section('title', 'Annonces')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des Annonces</h1>
        <a href="{{ route('annonces.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nouvelle annonce
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Créée par</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($annonces as $annonce)
                    <tr>
                        <td>{{ $annonce->titre }}</td>
                        <td>
                            @php
                                $badges = [
                                    'general'  => 'bg-secondary',
                                    'examen'   => 'bg-danger',
                                    'reunion'  => 'bg-primary',
                                    'paiement' => 'bg-warning text-dark',
                                    'autre'    => 'bg-info',
                                ];
                            @endphp
                            <span class="badge {{ $badges[$annonce->type] ?? 'bg-secondary' }}">
                                {{ ucfirst($annonce->type) }}
                            </span>
                        </td>
                        <td>
                            @if($annonce->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $annonce->createdBy?->name ?? '—' }}</td>
                        <td>{{ $annonce->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('annonces.edit', $annonce) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('annonces.destroy', $annonce) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Supprimer cette annonce ?')">
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
                        <td colspan="6" class="text-center text-muted">Aucune annonce enregistrée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $annonces->links() }}
        </div>
    </div>
</div>
@endsection
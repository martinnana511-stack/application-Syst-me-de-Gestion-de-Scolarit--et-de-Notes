@extends('layouts.app')

@section('title', 'Parents')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des Parents</h1>
        <a href="{{ route('parents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nouveau parent
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
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Élèves liés</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parents as $parent)
                    <tr>
                        <td>{{ $parent->user?->name }}</td>
                        <td>{{ $parent->user?->email }}</td>
                        <td>{{ $parent->user?->telephone ?? '—' }}</td>
                        <td>
                            @forelse($parent->students as $student)
                                <span class="badge bg-info">
                                    {{ $student->nom }} {{ $student->prenom }}
                                </span>
                            @empty
                                <span class="text-muted">Aucun élève</span>
                            @endforelse
                        </td>
                        <td>
                            @if($parent->user?->is_active)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-secondary">Inactif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('parents.show', $parent) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('parents.edit', $parent) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('parents.notifier', $parent) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-bell"></i>
                            </a>
                            <form action="{{ route('parents.toggle-active', $parent) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm {{ $parent->user?->is_active ? 'btn-secondary' : 'btn-success' }}">
                                    <i class="bi {{ $parent->user?->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('parents.destroy', $parent) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Supprimer ce parent ?')">
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
                        <td colspan="6" class="text-center text-muted">Aucun parent enregistré.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $parents->links() }}
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des utilisateurs')
@section('breadcrumb') / Paramètres / Utilisateurs@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0 small"><strong>{{ $users->total() }}</strong> utilisateur(s)</p>
    <a href="{{ route('settings.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus me-1"></i>Nouveau compte
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:32px;height:32px;background:var(--sgs-primary);
                                        color:#fff;font-weight:700;font-size:.75rem;flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <span class="fw-semibold small">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="small text-muted">{{ $user->email }}</td>
                    <td>
                        <span class="badge" style="background:{{ $user->role === 'gestionnaire' ? '#eaf2ff' : '#eafaf1' }};
                              color:{{ $user->role === 'gestionnaire' ? 'var(--sgs-primary)' : '#1e8449' }};">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $user->telephone ?? '—' }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success-subtle text-success">Actif</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">Inactif</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('settings.users.edit', $user) }}"
                               class="btn btn-xs btn-outline-secondary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('settings.users.toggle-active', $user) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                        title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                                    <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('settings.users.destroy', $user) }}"
                                  onsubmit="return confirm('Supprimer ce compte ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Aucun utilisateur.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white">{{ $users->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

@endsection
@push('styles')
<style>.btn-xs { padding:.2rem .45rem; font-size:.75rem; }</style>
@endpush
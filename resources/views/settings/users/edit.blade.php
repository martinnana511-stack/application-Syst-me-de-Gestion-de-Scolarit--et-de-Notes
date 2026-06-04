@extends('layouts.app')
@section('title', 'Modifier — ' . $user->name)
@section('page-title', 'Modifier le compte')
@section('breadcrumb')
    / <a href="{{ route('settings.users.index') }}">Utilisateurs</a> / {{ $user->name }}
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">

    {{-- Modifier infos --}}
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-person-gear me-2"></i>{{ $user->name }}</div>
        <div class="card-body">
            <form method="POST" action="{{ route('settings.users.update', $user) }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium small">Nom complet</label>
                        <input type="text" name="name" class="form-control form-control-sm"
                               value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-medium small">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm"
                               value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium small">Téléphone</label>
                        <input type="tel" name="telephone" class="form-control form-control-sm"
                               value="{{ old('telephone', $user->telephone) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium small">Rôle</label>
                        <select name="role" class="form-select form-select-sm">
                            <option value="enseignant" @selected($user->role === 'enseignant')>Enseignant</option>
                            <option value="gestionnaire" @selected($user->role === 'gestionnaire')>Gestionnaire</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary btn-sm">Annuler</a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check2 me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Réinitialiser mot de passe --}}
    <div class="card">
        <div class="card-header"><i class="bi bi-key me-2"></i>Réinitialiser le mot de passe</div>
        <div class="card-body">
            <form method="POST" action="{{ route('settings.users.reset-password', $user) }}">
                @csrf @method('PATCH')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium small">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium small">Confirmer</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="bi bi-key me-1"></i>Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection
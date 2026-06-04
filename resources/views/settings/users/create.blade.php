@extends('layouts.app')
@section('title', 'Nouveau compte')
@section('page-title', 'Créer un compte')
@section('breadcrumb')
    / <a href="{{ route('settings.users.index') }}">Utilisateurs</a> / Nouveau
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header"><i class="bi bi-person-gear me-2"></i>Nouveau compte</div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.users.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium small">Nom complet <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-sm"
                           value="{{ old('name') }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-medium small">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control form-control-sm"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control form-control-sm"
                           value="{{ old('telephone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Rôle <span class="text-danger">*</span></label>
                    <select name="role" class="form-select form-select-sm" required>
                        <option value="enseignant" @selected(old('role') === 'enseignant')>Enseignant</option>
                        <option value="gestionnaire" @selected(old('role') === 'gestionnaire')>Gestionnaire</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Mot de passe <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control form-control-sm" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium small">Confirmer le mot de passe <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary btn-sm">Annuler</a>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-check2 me-1"></i>Créer le compte
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
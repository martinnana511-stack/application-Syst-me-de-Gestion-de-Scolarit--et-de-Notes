@extends('layouts.app')
@section('title', 'Nouvelle année scolaire')
@section('page-title', 'Créer une année scolaire')
@section('breadcrumb')
    / <a href="{{ route('settings.academic-years.index') }}">Années scolaires</a> / Nouvelle
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header"><i class="bi bi-calendar3 me-2"></i>Nouvelle année scolaire</div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.academic-years.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium small">
                        Libellé <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="libelle" class="form-control form-control-sm"
                           value="{{ old('libelle') }}" placeholder="Ex: 2025-2026"
                           maxlength="20" required>
                    @error('libelle')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">
                        Date de début <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="date_debut" class="form-control form-control-sm"
                           value="{{ old('date_debut') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">
                        Date de fin <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="date_fin" class="form-control form-control-sm"
                           value="{{ old('date_fin') }}" required>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('settings.academic-years.index') }}"
                   class="btn btn-outline-secondary btn-sm">Annuler</a>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-check2 me-1"></i>Créer
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
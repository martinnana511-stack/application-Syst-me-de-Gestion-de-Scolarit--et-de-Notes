@extends('layouts.app')
@section('title', 'Nouveau trimestre')
@section('page-title', 'Ajouter un trimestre')
@section('breadcrumb')
    / <a href="{{ route('settings.academic-years.index') }}">Années scolaires</a>
    / {{ $year->libelle }} / Nouveau trimestre
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header">
        <i class="bi bi-calendar3 me-2"></i>
        Nouveau trimestre — {{ $year->libelle }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.terms.store', $year) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium small">
                        Numéro <span class="text-danger">*</span>
                    </label>
                    <select name="numero" class="form-select form-select-sm" required>
                        <option value="">— Choisir —</option>
                        <option value="1" @selected(old('numero') == '1')>1er Trimestre</option>
                        <option value="2" @selected(old('numero') == '2')>2ème Trimestre</option>
                        <option value="3" @selected(old('numero') == '3')>3ème Trimestre</option>
                    </select>
                    @error('numero')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">
                        Libellé <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="libelle" class="form-control form-control-sm"
                           value="{{ old('libelle') }}"
                           placeholder="Ex: 1er Trimestre" required>
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
                    <i class="bi bi-check2 me-1"></i>Créer le trimestre
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
@extends('layouts.app')
@section('title', 'Modifier — ' . $academicYear->libelle)
@section('page-title', 'Modifier l\'année scolaire')
@section('breadcrumb')
    / <a href="{{ route('settings.academic-years.index') }}">Années scolaires</a>
    / {{ $academicYear->libelle }}
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header">
        <i class="bi bi-calendar3 me-2"></i>{{ $academicYear->libelle }}
        @if($academicYear->is_active)
            <span class="badge bg-success ms-2">Active</span>
        @endif
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.academic-years.update', $academicYear) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium small">Libellé</label>
                    <input type="text" name="libelle" class="form-control form-control-sm"
                           value="{{ old('libelle', $academicYear->libelle) }}"
                           maxlength="20" required>
                    @error('libelle')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Date de début</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm"
                           value="{{ old('date_debut', $academicYear->date_debut->format('Y-m-d')) }}"
                           required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Date de fin</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm"
                           value="{{ old('date_fin', $academicYear->date_fin->format('Y-m-d')) }}"
                           required>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('settings.academic-years.index') }}"
                   class="btn btn-outline-secondary btn-sm">Annuler</a>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-check2 me-1"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
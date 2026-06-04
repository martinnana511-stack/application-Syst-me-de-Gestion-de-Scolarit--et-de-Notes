@extends('layouts.app')
@section('title', 'Modifier — ' . $term->libelle)
@section('page-title', 'Modifier le trimestre')
@section('breadcrumb')
    / <a href="{{ route('settings.academic-years.index') }}">Années scolaires</a>
    / {{ $term->libelle }}
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header">
        <i class="bi bi-calendar3 me-2"></i>{{ $term->libelle }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.terms.update', $term) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium small">Libellé</label>
                    <input type="text" name="libelle" class="form-control form-control-sm"
                           value="{{ old('libelle', $term->libelle) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Date de début</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm"
                           value="{{ old('date_debut', $term->date_debut->format('Y-m-d')) }}"
                           required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Date de fin</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm"
                           value="{{ old('date_fin', $term->date_fin->format('Y-m-d')) }}"
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
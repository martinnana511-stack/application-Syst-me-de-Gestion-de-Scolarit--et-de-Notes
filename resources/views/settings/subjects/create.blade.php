@extends('layouts.app')
@section('title', 'Nouvelle matière')
@section('page-title', 'Créer une matière')
@section('breadcrumb')
    / <a href="{{ route('settings.subjects.index') }}">Matières</a> / Nouvelle
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header"><i class="bi bi-journal-bookmark me-2"></i>Nouvelle matière</div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.subjects.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-medium small">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="nom" class="form-control form-control-sm"
                           value="{{ old('nom') }}" placeholder="Ex: Mathématiques" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control form-control-sm"
                           value="{{ old('code') }}" placeholder="Ex: MATH" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Coefficient <span class="text-danger">*</span></label>
                    <input type="number" name="coefficient" class="form-control form-control-sm"
                           value="{{ old('coefficient', 1) }}" min="0.5" max="10" step="0.5" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Note maximale <span class="text-danger">*</span></label>
                    <select name="note_max" class="form-select form-select-sm" required>
                        <option value="20" @selected(old('note_max', 20) == 20)>20</option>
                        <option value="10" @selected(old('note_max') == 10)>10</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('settings.subjects.index') }}" class="btn btn-outline-secondary btn-sm">Annuler</a>
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
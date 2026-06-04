@extends('layouts.app')
@section('title', 'Modifier — ' . $subject->nom)
@section('page-title', 'Modifier la matière')
@section('breadcrumb')
    / <a href="{{ route('settings.subjects.index') }}">Matières</a> / {{ $subject->nom }}
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header"><i class="bi bi-journal-bookmark me-2"></i>{{ $subject->nom }}</div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.subjects.update', $subject) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-medium small">Nom</label>
                    <input type="text" name="nom" class="form-control form-control-sm"
                           value="{{ old('nom', $subject->nom) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Code</label>
                    <input type="text" name="code" class="form-control form-control-sm"
                           value="{{ old('code', $subject->code) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Coefficient</label>
                    <input type="number" name="coefficient" class="form-control form-control-sm"
                           value="{{ old('coefficient', $subject->coefficient) }}"
                           min="0.5" max="10" step="0.5" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Note maximale</label>
                    <select name="note_max" class="form-select form-select-sm">
                        <option value="20" @selected($subject->note_max == 20)>20</option>
                        <option value="10" @selected($subject->note_max == 10)>10</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               id="is_active" value="1" @checked($subject->is_active)>
                        <label class="form-check-label small" for="is_active">Matière active</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('settings.subjects.index') }}" class="btn btn-outline-secondary btn-sm">Annuler</a>
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
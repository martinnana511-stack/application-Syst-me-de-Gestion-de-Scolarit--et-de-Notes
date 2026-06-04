@extends('layouts.app')
@section('title', 'Modifier — ' . $schoolClass->nom)
@section('page-title', 'Modifier la classe')
@section('breadcrumb')
    / <a href="{{ route('settings.classes.index') }}">Classes</a> / {{ $schoolClass->nom }}
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header"><i class="bi bi-building me-2"></i>{{ $schoolClass->nom }}</div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.classes.update', $schoolClass) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Nom de la classe</label>
                    <input type="text" name="nom" class="form-control form-control-sm"
                           value="{{ old('nom', $schoolClass->nom) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Enseignant titulaire</label>
                    <select name="teacher_id" class="form-select form-select-sm">
                        <option value="">— Aucun —</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" @selected($schoolClass->teacher_id == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Effectif maximum</label>
                    <input type="number" name="effectif_max" class="form-control form-control-sm"
                           value="{{ old('effectif_max', $schoolClass->effectif_max) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Frais d'inscription (F)</label>
                    <input type="number" name="frais_inscription" class="form-control form-control-sm"
                           value="{{ old('frais_inscription', $schoolClass->frais_inscription) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Frais de scolarité (F)</label>
                    <input type="number" name="frais_scolarite_annuel" class="form-control form-control-sm"
                           value="{{ old('frais_scolarite_annuel', $schoolClass->frais_scolarite_annuel) }}">
                </div>

                {{-- Section matières dans edit.blade.php --}}
                <div class="col-12">
                    <hr>
                    <label class="form-label fw-medium">
                        <i class="bi bi-journal-bookmark me-2"></i>Matières enseignées
                    </label>
                    <div class="row g-2 mt-1">
                        @foreach($subjects as $subject)
                        @php $checked = $schoolClass->subjects->contains($subject->id); @endphp
                        <div class="col-md-4 col-sm-6">
                            <div class="form-check border rounded p-2"
                                style="cursor:pointer;{{ $checked ? 'background:#eaf2ff;' : '' }}"
                                onclick="toggleSubject({{ $subject->id }})">
                                <input class="form-check-input"
                                    type="checkbox"
                                    name="subjects[]"
                                    id="subject_{{ $subject->id }}"
                                    value="{{ $subject->id }}"
                                    @checked($checked)>
                                <label class="form-check-label w-100" style="cursor:pointer;"
                                    for="subject_{{ $subject->id }}">
                                    <span class="fw-semibold small">{{ $subject->nom }}</span>
                                    <span class="badge ms-1"
                                        style="background:#eaf2ff;color:var(--sgs-primary);font-size:.65rem;">
                                        ×{{ $subject->coefficient }}
                                    </span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('settings.classes.index') }}" class="btn btn-outline-secondary btn-sm">Annuler</a>
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

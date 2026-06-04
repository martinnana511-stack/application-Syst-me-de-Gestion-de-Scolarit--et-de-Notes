@extends('layouts.app')
@section('title', 'Nouvelle classe')
@section('page-title', 'Créer une classe')
@section('breadcrumb')
    / <a href="{{ route('settings.classes.index') }}">Classes</a> / Nouvelle
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
    <div class="card-header"><i class="bi bi-building me-2"></i>Nouvelle classe</div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.classes.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Niveau <span class="text-danger">*</span></label>
                    <select name="niveau" class="form-select form-select-sm" required>
                        <option value="">— Choisir —</option>
                        @foreach(['CP1','CP2','CE1','CE2','CM1','CM2'] as $n)
                            <option value="{{ $n }}" @selected(old('niveau') === $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Nom de la classe <span class="text-danger">*</span></label>
                    <input type="text" name="nom" class="form-control form-control-sm"
                           value="{{ old('nom') }}" placeholder="Ex: CP1-A" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Année scolaire <span class="text-danger">*</span></label>
                    <select name="academic_year_id" class="form-select form-select-sm" required>
                        @foreach($years as $y)
                            <option value="{{ $y->id }}" @selected($y->is_active)>{{ $y->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium small">Enseignant titulaire</label>
                    <select name="teacher_id" class="form-select form-select-sm">
                        <option value="">— Aucun —</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" @selected(old('teacher_id') == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Effectif maximum</label>
                    <input type="number" name="effectif_max" class="form-control form-control-sm"
                           value="{{ old('effectif_max', 40) }}" min="1" max="100">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Frais d'inscription (F)</label>
                    <input type="number" name="frais_inscription" class="form-control form-control-sm"
                           value="{{ old('frais_inscription', 5000) }}" min="0" step="500">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium small">Frais de scolarité annuel (F)</label>
                    <input type="number" name="frais_scolarite_annuel" class="form-control form-control-sm"
                           value="{{ old('frais_scolarite_annuel', 60000) }}" min="0" step="1000">
                </div>

                {{-- Section matières --}}
                <div class="col-12">
                    <hr>
                    <label class="form-label fw-medium">
                        <i class="bi bi-journal-bookmark me-2"></i>
                        Matières enseignées <span class="text-danger">*</span>
                    </label>
                    <div class="row g-2 mt-1">
                        @foreach($subjects as $subject)
                        <div class="col-md-4 col-sm-6">
                            <div class="form-check border rounded p-2"
                                 style="cursor:pointer;"
                                 onclick="toggleSubject({{ $subject->id }})">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="subjects[]"
                                       id="subject_{{ $subject->id }}"
                                       value="{{ $subject->id }}"
                                       @checked(in_array($subject->id, old('subjects', [])))>
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
                    @error('subjects')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

            </div>
            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('settings.classes.index') }}" class="btn btn-outline-secondary btn-sm">Annuler</a>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-check2 me-1"></i>Créer la classe
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
function toggleSubject(id) {
    const checkbox = document.getElementById('subject_' + id);
    checkbox.checked = !checkbox.checked;
    checkbox.closest('.form-check').style.background = checkbox.checked ? '#eaf2ff' : '';
}

// Colorer les cases déjà cochées au chargement
document.querySelectorAll('.form-check-input:checked').forEach(cb => {
    cb.closest('.form-check').style.background = '#eaf2ff';
});
</script>
@endpush
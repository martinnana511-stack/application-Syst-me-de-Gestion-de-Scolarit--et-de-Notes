@extends('layouts.app')
@section('title', isset($student) ? 'Modifier un élève' : 'Inscrire un élève')
@section('page-title', isset($student) ? 'Modifier l\'élève' : 'Inscrire un élève')
@section('breadcrumb')
    / <a href="{{ route('students.index') }}">Élèves</a>
    / {{ isset($student) ? $student->nom_complet : 'Nouvelle inscription' }}
@endsection

@section('content')

<form method="POST"
      action="{{ isset($student) ? route('students.update', $student) : route('students.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if(isset($student)) @method('PUT') @endif

    <div class="row g-3">

        {{-- ── Colonne gauche ──────────────────────── --}}
        <div class="col-lg-4">

            {{-- Photo --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-camera me-2"></i>Photo de l'élève</div>
                <div class="card-body text-center">
                    <div id="photo-preview" class="mb-3">
                        <img src="{{ $student->photo_url ?? asset('images/avatar-default.png') }}"
                             id="preview-img"
                             class="rounded-circle object-fit-cover"
                             style="width:100px;height:100px;border:3px solid #e4e8ed;">
                    </div>
                    <input type="file" name="photo" id="photo-input"
                           class="form-control form-control-sm @error('photo') is-invalid @enderror"
                           accept="image/jpeg,image/png"
                           onchange="previewPhoto(event)">
                    <div class="form-text">JPG/PNG · max 2 Mo</div>
                    @error('photo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Classe (création uniquement) --}}
            @if(!isset($student))
            <div class="card">
                <div class="card-header"><i class="bi bi-building me-2"></i>Affectation</div>
                <div class="card-body">
                    <label class="form-label fw-medium small">Classe <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-select form-select-sm @error('class_id') is-invalid @enderror" required>
                        <option value="">— Choisir une classe —</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}"
                                    @selected(old('class_id') == $c->id)
                                    {{ $c->effectif_actuel >= $c->effectif_max ? 'disabled' : '' }}>
                                {{ $c->nom }}
                                ({{ $c->effectif_actuel }}/{{ $c->effectif_max }})
                                — {{ number_format($c->frais_scolarite_annuel, 0, ',', ' ') }} F
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            @endif

        </div>

        {{-- ── Colonne droite ───────────────────────── --}}
        <div class="col-lg-8">

            {{-- Identité --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-person me-2"></i>Identité de l'élève</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom"
                                   class="form-control form-control-sm @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $student->nom ?? '') }}"
                                   placeholder="Ex : OUÉDRAOGO" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Prénom(s) <span class="text-danger">*</span></label>
                            <input type="text" name="prenom"
                                   class="form-control form-control-sm @error('prenom') is-invalid @enderror"
                                   value="{{ old('prenom', $student->prenom ?? '') }}"
                                   placeholder="Ex : Issouf" required>
                            @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Date de naissance <span class="text-danger">*</span></label>
                            <input type="date" name="date_naissance"
                                   class="form-control form-control-sm @error('date_naissance') is-invalid @enderror"
                                   value="{{ old('date_naissance', isset($student) ? $student->date_naissance->format('Y-m-d') : '') }}"
                                   required>
                            @error('date_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Lieu de naissance</label>
                            <input type="text" name="lieu_naissance"
                                   class="form-control form-control-sm"
                                   value="{{ old('lieu_naissance', $student->lieu_naissance ?? '') }}"
                                   placeholder="Ex : Ouagadougou">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Sexe <span class="text-danger">*</span></label>
                            <select name="sexe" class="form-select form-select-sm @error('sexe') is-invalid @enderror" required>
                                <option value="">— Choisir —</option>
                                <option value="M" @selected(old('sexe', $student->sexe ?? '') === 'M')>Masculin</option>
                                <option value="F" @selected(old('sexe', $student->sexe ?? '') === 'F')>Féminin</option>
                            </select>
                            @error('sexe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Adresse</label>
                            <input type="text" name="adresse"
                                   class="form-control form-control-sm"
                                   value="{{ old('adresse', $student->adresse ?? '') }}"
                                   placeholder="Secteur, quartier…">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Famille / Tuteur --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-house-heart me-2"></i>Famille & Tuteur</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Nom du père</label>
                            <input type="text" name="nom_pere" class="form-control form-control-sm"
                                   value="{{ old('nom_pere', $student->nom_pere ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Nom de la mère</label>
                            <input type="text" name="nom_mere" class="form-control form-control-sm"
                                   value="{{ old('nom_mere', $student->nom_mere ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small">Tuteur légal</label>
                            <input type="text" name="tuteur_nom" class="form-control form-control-sm"
                                   value="{{ old('tuteur_nom', $student->tuteur_nom ?? '') }}"
                                   placeholder="Si différent des parents">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small">Téléphone principal</label>
                            <input type="tel" name="tuteur_telephone" class="form-control form-control-sm"
                                   value="{{ old('tuteur_telephone', $student->tuteur_telephone ?? '') }}"
                                   placeholder="+226 XX XX XX XX">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small">Téléphone secondaire</label>
                            <input type="tel" name="tuteur_telephone2" class="form-control form-control-sm"
                                   value="{{ old('tuteur_telephone2', $student->tuteur_telephone2 ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2 me-1"></i>
                    {{ isset($student) ? 'Enregistrer les modifications' : 'Inscrire l\'élève' }}
                </button>
            </div>

        </div>{{-- /col --}}
    </div>{{-- /row --}}
</form>

@endsection

@push('scripts')
<script>
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        document.getElementById('preview-img').src = URL.createObjectURL(file);
    }
}
</script>
@endpush

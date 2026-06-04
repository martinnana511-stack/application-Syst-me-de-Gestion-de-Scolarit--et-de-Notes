@extends('layouts.app')
@section('title', 'Modifier — ' . $student->nom_complet)
@section('page-title', 'Modifier l\'élève')
@section('breadcrumb')
    / <a href="{{ route('students.index') }}">Élèves</a>
    / <a href="{{ route('students.show', $student) }}">{{ $student->nom_complet }}</a>
    / Modifier
@endsection

@section('content')

<form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-3">

        {{-- Colonne gauche --}}
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-camera me-2"></i>Photo de l'élève</div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="{{ $student->photo_url }}" id="preview-img"
                             class="rounded-circle object-fit-cover"
                             style="width:100px;height:100px;border:3px solid #e4e8ed;">
                    </div>
                    <input type="file" name="photo" class="form-control form-control-sm"
                           accept="image/jpeg,image/png"
                           onchange="previewPhoto(event)">
                    <div class="form-text">JPG/PNG · max 2 Mo</div>
                </div>
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="col-lg-8">

            {{-- Identité --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-person me-2"></i>Identité de l'élève</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control form-control-sm @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $student->nom) }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Prénom(s) <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" class="form-control form-control-sm @error('prenom') is-invalid @enderror"
                                   value="{{ old('prenom', $student->prenom) }}" required>
                            @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Date de naissance <span class="text-danger">*</span></label>
                            <input type="date" name="date_naissance" class="form-control form-control-sm"
                                   value="{{ old('date_naissance', $student->date_naissance->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Lieu de naissance</label>
                            <input type="text" name="lieu_naissance" class="form-control form-control-sm"
                                   value="{{ old('lieu_naissance', $student->lieu_naissance) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Sexe <span class="text-danger">*</span></label>
                            <select name="sexe" class="form-select form-select-sm" required>
                                <option value="M" @selected(old('sexe', $student->sexe) === 'M')>Masculin</option>
                                <option value="F" @selected(old('sexe', $student->sexe) === 'F')>Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Adresse</label>
                            <input type="text" name="adresse" class="form-control form-control-sm"
                                   value="{{ old('adresse', $student->adresse) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Famille --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-house-heart me-2"></i>Famille & Tuteur</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Nom du père</label>
                            <input type="text" name="nom_pere" class="form-control form-control-sm"
                                   value="{{ old('nom_pere', $student->nom_pere) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Nom de la mère</label>
                            <input type="text" name="nom_mere" class="form-control form-control-sm"
                                   value="{{ old('nom_mere', $student->nom_mere) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small">Tuteur légal</label>
                            <input type="text" name="tuteur_nom" class="form-control form-control-sm"
                                   value="{{ old('tuteur_nom', $student->tuteur_nom) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small">Téléphone principal</label>
                            <input type="tel" name="tuteur_telephone" class="form-control form-control-sm"
                                   value="{{ old('tuteur_telephone', $student->tuteur_telephone) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium small">Téléphone secondaire</label>
                            <input type="tel" name="tuteur_telephone2" class="form-control form-control-sm"
                                   value="{{ old('tuteur_telephone2', $student->tuteur_telephone2) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2 me-1"></i>Enregistrer les modifications
                </button>
            </div>

        </div>
    </div>
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
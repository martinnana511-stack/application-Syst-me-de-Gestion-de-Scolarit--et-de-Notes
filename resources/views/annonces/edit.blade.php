@extends('layouts.app')

@section('title', 'Modifier Annonce')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Modifier une Annonce</h1>
        <a href="{{ route('annonces.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('annonces.update', $annonce) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                    <input type="text" name="titre" id="titre"
                           class="form-control @error('titre') is-invalid @enderror"
                           value="{{ old('titre', $annonce->titre) }}" required>
                    @error('titre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">-- Sélectionner un type --</option>
                        <option value="general"  {{ old('type', $annonce->type) == 'general'  ? 'selected' : '' }}>Général</option>
                        <option value="examen"   {{ old('type', $annonce->type) == 'examen'   ? 'selected' : '' }}>Examen</option>
                        <option value="reunion"  {{ old('type', $annonce->type) == 'reunion'  ? 'selected' : '' }}>Réunion</option>
                        <option value="paiement" {{ old('type', $annonce->type) == 'paiement' ? 'selected' : '' }}>Paiement</option>
                        <option value="autre"    {{ old('type', $annonce->type) == 'autre'    ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                    <textarea name="contenu" id="contenu" rows="6"
                              class="form-control @error('contenu') is-invalid @enderror"
                              required>{{ old('contenu', $annonce->contenu) }}</textarea>
                    @error('contenu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active"
                               class="form-check-input" value="1"
                               {{ old('is_active', $annonce->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Annonce active</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Enregistrer les modifications
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
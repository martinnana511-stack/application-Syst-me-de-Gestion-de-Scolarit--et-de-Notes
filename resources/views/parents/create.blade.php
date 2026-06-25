@extends('layouts.app')

@section('title', 'Nouveau Parent')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Nouveau Parent</h1>
        <a href="{{ route('parents.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    {{-- Formulaire de filtre (séparé) --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('parents.create') }}" class="row g-3">
                <div class="col-md-5">
                    <select name="class_id" class="form-select">
                        <option value="">-- Toutes les classes --</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ request('class_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Rechercher par nom..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Formulaire principal --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('parents.store') }}" method="POST">
                @csrf

                <h5 class="mb-3 text-primary">Informations du compte</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" name="telephone" id="telephone"
                               class="form-control @error('telephone') is-invalid @enderror"
                               value="{{ old('telephone') }}">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="telephone_urgence" class="form-label">Téléphone d'urgence</label>
                        <input type="text" name="telephone_urgence" id="telephone_urgence"
                               class="form-control @error('telephone_urgence') is-invalid @enderror"
                               value="{{ old('telephone_urgence') }}">
                        @error('telephone_urgence')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="profession" class="form-label">Profession</label>
                        <input type="text" name="profession" id="profession"
                               class="form-control"
                               value="{{ old('profession') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" name="adresse" id="adresse"
                               class="form-control"
                               value="{{ old('adresse') }}">
                    </div>
                </div>

                <h5 class="mb-3 text-primary">Élèves liés</h5>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Sélectionner</th>
                                <th>Élève</th>
                                <th>Lien de parenté</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="students[]"
                                           value="{{ $student->id }}"
                                           class="form-check-input"
                                           {{ in_array($student->id, old('students', [])) ? 'checked' : '' }}>
                                </td>
                                <td>{{ $student->nom }} {{ $student->prenom }}</td>
                                <td>
                                    <select name="lien_parente[{{ $student->id }}]"
                                            class="form-select form-select-sm">
                                        <option value="pere">Père</option>
                                        <option value="mere">Mère</option>
                                        <option value="tuteur">Tuteur</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    Aucun élève trouvé.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Enregistrer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
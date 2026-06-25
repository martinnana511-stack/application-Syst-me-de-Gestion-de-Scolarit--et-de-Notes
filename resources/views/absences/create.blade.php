@extends('layouts.app')

@section('title', 'Nouvelle Absence')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Nouvelle Absence</h1>
        <a href="{{ route('absences.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    {{-- Formulaire de filtre (séparé) --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('absences.create') }}" class="row g-3">
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
            <form action="{{ route('absences.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="student_id" class="form-label">Élève <span class="text-danger">*</span></label>
                    <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                        <option value="">-- Sélectionner un élève --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->nom }} {{ $student->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" id="date"
                           class="form-control @error('date') is-invalid @enderror"
                           value="{{ old('date', date('Y-m-d')) }}" required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="motif" class="form-label">Motif</label>
                    <input type="text" name="motif" id="motif"
                           class="form-control @error('motif') is-invalid @enderror"
                           value="{{ old('motif') }}" placeholder="Ex: Maladie, Voyage...">
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="justifie" id="justifie"
                               class="form-check-input" value="1"
                               {{ old('justifie') ? 'checked' : '' }}>
                        <label for="justifie" class="form-check-label">Absence justifiée</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Enregistrer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Envoyer des Notifications</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Formulaire envoi -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Nouvelle notification</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('notifications.envoyer') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" name="titre" id="titre"
                                   class="form-control @error('titre') is-invalid @enderror"
                                   value="{{ old('titre') }}" required
                                   placeholder="Ex: Réunion de parents">
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" rows="4"
                                      class="form-control @error('message') is-invalid @enderror"
                                      required placeholder="Rédigez votre message...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="cible" class="form-label">Destinataires <span class="text-danger">*</span></label>
                            <select name="cible" id="cible" class="form-select" required>
                                <option value="tous">Tous les parents</option>
                                <option value="annonce">Liés à une annonce</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send"></i> Envoyer la notification
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Dernières annonces -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Dernières annonces publiées</h5>
                </div>
                <div class="card-body">
                    @forelse($annonces as $annonce)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold text-primary mb-1">{{ $annonce->titre }}</h6>
                                <p class="text-muted small mb-1">{{ Str::limit($annonce->contenu, 100) }}</p>
                                <small class="text-muted">
                                    {{ $annonce->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary"
                                    onclick="remplirFormulaire('{{ $annonce->titre }}', '{{ addslashes(Str::limit($annonce->contenu, 200)) }}')">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">Aucune annonce disponible.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function remplirFormulaire(titre, message) {
        document.getElementById('titre').value = titre;
        document.getElementById('message').value = message;
        document.getElementById('cible').value = 'tous';
        window.scrollTo(0, 0);
    }
</script>
@endpush
@endsection
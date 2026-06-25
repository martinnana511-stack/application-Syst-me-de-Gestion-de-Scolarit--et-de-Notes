@extends('layouts.app')

@section('title', 'Notifier un Parent')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Envoyer une notification</h1>
        <a href="{{ route('parents.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Destinataire</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Nom</th>
                            <td>{{ $parent->user?->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $parent->user?->email }}</td>
                        </tr>
                        <tr>
                            <th>Téléphone</th>
                            <td>{{ $parent->user?->telephone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Token FCM</th>
                            <td>
                                @if($parent->fcm_token)
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-danger">Non disponible</span>
                                    <small class="text-muted d-block mt-1">
                                        Le parent doit se connecter à l'application mobile.
                                    </small>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Message</h5>
                </div>
                <div class="card-body">
                    @if(!$parent->fcm_token)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Ce parent n'a pas encore de token FCM. Il doit d'abord se connecter à l'application mobile SGS Parent.
                        </div>
                    @endif

                    <form action="{{ route('parents.notifier.envoyer', $parent) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" name="titre" id="titre"
                                   class="form-control @error('titre') is-invalid @enderror"
                                   value="{{ old('titre') }}" required
                                   placeholder="Ex: Convocation">
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" rows="5"
                                      class="form-control @error('message') is-invalid @enderror"
                                      required placeholder="Rédigez votre message...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100"
                                {{ !$parent->fcm_token ? 'disabled' : '' }}>
                            <i class="bi bi-send"></i> Envoyer la notification
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
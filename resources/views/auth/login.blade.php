<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — SGS École Primaire</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
        }
        .school-logo {
            width: 72px;
            height: 72px;
            background: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: #fff;
        }
        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">

                {{-- En-tête --}}
                <div class="text-center mb-4">
                    <div class="school-logo">
                        <span>&#127979;</span>
                    </div>
                    <h1 class="h4 fw-semibold text-dark mb-1">SGS École Primaire</h1>
                    <p class="text-muted small">Système de Gestion de Scolarité</p>
                </div>

                {{-- Messages d'erreur --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                @endif

                {{-- Formulaire --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Adresse e-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="adminecole@gmail.com"
                            required
                            autofocus
                            autocomplete="email"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Mot de passe</label>
                        <div class="input-group">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                                autocomplete="current-password"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                onclick="togglePassword()"
                                tabindex="-1"
                                title="Afficher / masquer"
                            >
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small" for="remember">Se souvenir de moi</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-medium">
                        Se connecter
                    </button>
                </form>

            </div>
        </div>

        {{-- Indication des rôles --}}
        <div class="text-center mt-3">
            <span class="text-muted small me-2">Accès :</span>
            <span class="badge bg-primary role-badge me-1">Gestionnaire</span>
            <span class="badge bg-success role-badge">Enseignant</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>

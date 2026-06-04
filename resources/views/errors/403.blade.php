<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé — SGS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="text-center" style="max-width: 420px;">
            <div class="display-1 fw-bold text-danger mb-3">403</div>
            <h1 class="h4 fw-semibold mb-2">Accès refusé</h1>
            <p class="text-muted mb-4">
                {{ $message ?? 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.' }}
            </p>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">
                &larr; Retour
            </a>
            @auth
                @if(auth()->user()->isGestionnaire())
                    <a href="{{ route('dashboard.index') }}" class="btn btn-primary">
                        Tableau de bord
                    </a>
                @else
                    <a href="{{ route('grades.index') }}" class="btn btn-primary">
                        Mes notes
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Se connecter</a>
            @endauth
        </div>
    </div>
</body>
</html>

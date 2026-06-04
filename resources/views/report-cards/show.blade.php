@extends('layouts.app')
@section('title', 'Classement — ' . $class->nom)
@section('page-title', 'Classement — ' . $class->nom . ' · ' . $term->libelle)
@section('breadcrumb')
    / <a href="{{ route('report-cards.index') }}">Bulletins</a>
    / {{ $class->nom }}
@endsection

@section('content')

{{-- ── Statistiques de la classe ───────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eaf2ff;">👥</div>
            <div>
                <div class="stat-value">{{ $stats['effectif'] }}</div>
                <div class="stat-label">Élèves classés</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eafaf1;">📊</div>
            <div>
                <div class="stat-value">{{ number_format($stats['moyenne_classe'], 2) }}</div>
                <div class="stat-label">Moyenne de la classe</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef9e7;">🏆</div>
            <div>
                <div class="stat-value">{{ number_format($stats['meilleure_moyenne'], 2) }}</div>
                <div class="stat-label">Meilleure moyenne</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdedec;">📉</div>
            <div>
                <div class="stat-value">{{ number_format($stats['plus_faible'], 2) }}</div>
                <div class="stat-label">Plus faible moyenne</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Répartition des mentions ─────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ol me-2"></i>Classement des élèves</span>
                <div class="d-flex gap-2">
                    @can('manage-admin')
                    <form method="POST"
                          action="{{ route('report-cards.recalculate', [$class, $term]) }}"
                          onsubmit="return confirm('Recalculer toutes les moyennes ?')">
                        @csrf
                        <button class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-arrow-clockwise me-1"></i>Recalculer
                        </button>
                    </form>
                    @endcan
                    <a href="{{ route('grades.export', [$class, $term]) }}"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download me-1"></i>Export CSV
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:50px;">Rang</th>
                                <th>Élève</th>
                                <th>Sexe</th>
                                <th>Moyenne</th>
                                <th>Mention</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportCards as $rc)
                            @php
                                $moy    = (float)$rc->moyenne_generale;
                                $color  = $moy >= 14 ? 'success' : ($moy >= 10 ? 'warning' : 'danger');
                                $medal  = match($rc->rang) { 1 => '🥇', 2 => '🥈', 3 => '🥉', default => '' };
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <span class="fw-bold" style="font-family:var(--font-heading);font-size:1rem;">
                                        {{ $medal ?: $rc->rang }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $rc->enrollment->student->photo_url }}"
                                             class="rounded-circle" style="width:30px;height:30px;object-fit:cover;">
                                        <div>
                                            <div class="fw-semibold small">{{ $rc->enrollment->student->nom_complet }}</div>
                                            <div style="font-size:.7rem;color:#aaa;">{{ $rc->enrollment->student->matricule }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $rc->enrollment->student->sexe === 'M' ? '♂' : '♀' }}</td>
                                <td>
                                    <span class="fw-bold" style="font-size:1.05rem;color:var(--sgs-primary);">
                                        {{ number_format($moy, 2) }}
                                    </span>
                                    <span class="text-muted small">/20</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">
                                        {{ $rc->mention }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('report-cards.pdf', [$rc->enrollment, $term]) }}"
                                       class="btn btn-xs btn-outline-primary" title="Bulletin PDF" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-clipboard-x fs-2 d-block mb-2 opacity-25"></i>
                                    Aucun bulletin calculé. Saisissez d'abord les notes.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Répartition mentions ─────────────────────── --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-pie-chart me-2"></i>Répartition des mentions</div>
            <div class="card-body">
                <canvas id="chartMentions" height="180"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Détail</div>
            <div class="card-body p-0">
                @foreach($stats['nb_mentions'] as $mention => $nb)
                @php
                    $pct   = $stats['effectif'] > 0 ? round($nb / $stats['effectif'] * 100) : 0;
                    $color = match(true) {
                        str_contains($mention, 'Excellent')  => '#1e8449',
                        str_contains($mention, 'Très')       => '#27ae60',
                        str_contains($mention, 'Bien')       => '#2980b9',
                        str_contains($mention, 'Passable')   => '#f39c12',
                        default                              => '#e74c3c',
                    };
                @endphp
                <div class="px-3 py-2 border-bottom">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-semibold">{{ $mention }}</span>
                        <span class="text-muted">{{ $nb }} élève(s) · {{ $pct }}%</span>
                    </div>
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const mentionsData = @json($stats['nb_mentions']);
new Chart(document.getElementById('chartMentions'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(mentionsData),
        datasets: [{
            data: Object.values(mentionsData),
            backgroundColor: ['#1e8449', '#27ae60', '#2980b9', '#f39c12', '#e74c3c'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 } } }
        }
    }
});
</script>
@endpush

@push('styles')
<style>.btn-xs { padding:.2rem .45rem; font-size:.75rem; }</style>
@endpush

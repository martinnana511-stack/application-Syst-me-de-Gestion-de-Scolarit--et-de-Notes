@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('breadcrumb')@endsection

@section('content')

{{-- ── Cartes statistiques ───────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eaf2ff;">👦</div>
            <div>
                <div class="stat-value">{{ number_format($stats['totalEleves']) }}</div>
                <div class="stat-label">Élèves inscrits</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eafaf1;">🏫</div>
            <div>
                <div class="stat-value">{{ $stats['totalClasses'] }}</div>
                <div class="stat-label">Classes actives</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef9e7;">💰</div>
            <div>
                <div class="stat-value">{{ number_format($stats['totalCollecte'], 0, ',', ' ') }}</div>
                <div class="stat-label">F CFA collectés</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdedec;">⚠️</div>
            <div>
                <div class="stat-value text-danger">{{ $stats['nbImpayes'] }}</div>
                <div class="stat-label">Élèves en impayé</div>
            </div>
        </div>
    </div>

</div>

{{-- ── Recouvrement global ──────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart me-2"></i>Encaissements mensuels</span>
            </div>
            <div class="card-body">
                <canvas id="chartMensuel" height="90"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-pie-chart me-2"></i>Taux de recouvrement</div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center gap-3">
                <canvas id="chartDonut" width="160" height="160" style="max-width:160px;"></canvas>
                <div class="text-center">
                    <div style="font-family:var(--font-heading);font-size:2rem;font-weight:700;color:var(--sgs-primary);">
                        {{ $stats['tauxRecouvrement'] }}%
                    </div>
                    <div class="text-muted small">
                        {{ number_format($stats['totalCollecte'], 0, ',', ' ') }} F /
                        {{ number_format($stats['totalAttendu'], 0, ',', ' ') }} F
                    </div>
                    <div class="progress mt-2" style="height:8px;">
                        <div class="progress-bar" style="width:{{ $stats['tauxRecouvrement'] }}%;background:var(--sgs-accent);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Stats par classe + Impayés ──────────────────── --}}
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building me-2"></i>Recouvrement par classe</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Classe</th>
                                <th>Élèves</th>
                                <th>Attendu</th>
                                <th>Collecté</th>
                                <th>Restant</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parClasse as $c)
                            @php
                                $pct = $c['total_attendu'] > 0
                                    ? round($c['total_collecte'] / $c['total_attendu'] * 100)
                                    : 100;
                                $color = $pct >= 80 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $c['nom'] }}</span>
                                    <span class="badge badge-niveau ms-1"
                                          style="background:#eaf2ff;color:var(--sgs-primary);">{{ $c['niveau'] }}</span>
                                </td>
                                <td>{{ $c['nb_eleves'] }}</td>
                                <td class="text-muted">{{ number_format($c['total_attendu'], 0, ',', ' ') }}</td>
                                <td class="text-success fw-semibold">{{ number_format($c['total_collecte'], 0, ',', ' ') }}</td>
                                <td class="text-danger">{{ number_format($c['total_restant'], 0, ',', ' ') }}</td>
                                <td style="min-width:90px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:6px;">
                                            <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%;"></div>
                                        </div>
                                        <span class="small text-muted">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Top impayés</span>
                <a href="{{ route('dashboard.impayes') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                @forelse($impayes as $e)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:34px;height:34px;background:#fdedec;color:var(--sgs-danger);font-weight:700;font-size:.8rem;">
                        {{ strtoupper(substr($e->student->nom, 0, 2)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate small">{{ $e->student->nom_complet }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $e->schoolClass->nom }}</div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="text-danger fw-semibold small">
                            {{ number_format($e->reste, 0, ',', ' ') }} F
                        </div>
                        <div style="font-size:.7rem;color:#aaa;">restant</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                    <p class="mt-2 mb-0 small">Aucun impayé 🎉</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Graphique mensuel ──────────────────────────────────
const mensuelData = @json($stats['evolutionMensuelle']);
const labels = Object.keys(mensuelData).map(m => {
    const [y, mo] = m.split('-');
    return new Date(y, mo - 1).toLocaleDateString('fr-FR', { month: 'short', year: '2-digit' });
});
const values = Object.values(mensuelData);

new Chart(document.getElementById('chartMensuel'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Encaissements (F CFA)',
            data: values,
            backgroundColor: 'rgba(26, 82, 118, 0.75)',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => v.toLocaleString('fr-FR') + ' F' } }
        }
    }
});

// ── Donut recouvrement ─────────────────────────────────
const collecte  = {{ $stats['totalCollecte'] }};
const restant   = {{ $stats['totalRestant'] }};
new Chart(document.getElementById('chartDonut'), {
    type: 'doughnut',
    data: {
        labels: ['Collecté', 'Restant'],
        datasets: [{
            data: [collecte, restant],
            backgroundColor: ['#1e8449', '#e8ecf0'],
            borderWidth: 0,
        }]
    },
    options: {
        cutout: '72%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 10px;
    color: #1c2833;
    background: #fff;
}

/* ── Page ─────────────────────────────────────────── */
.page {
    width: 210mm; /* Format A4 */
    min-height: 280mm;
    padding: 20px 24px 16px;
    position: relative;
}

/* ── Bandeau supérieur ────────────────────────────── */
.top-band {
    background: #1a5276;
    margin: -14px -16px 14px;
    padding: 10px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.top-band .school-name {
    font-size: 12px; font-weight: bold;
    color: #fff; letter-spacing: .02em;
}
.top-band .school-sub {
    font-size: 7.5px; color: rgba(255,255,255,.6);
    text-transform: uppercase; letter-spacing: .06em;
    margin-top: 1px;
}
.top-band .doc-badge {
    background: #e67e22; color: #fff;
    font-size: 8.5px; font-weight: bold;
    padding: 4px 10px; border-radius: 4px;
    text-transform: uppercase; letter-spacing: .06em;
}

/* ── Bandeau numéro ───────────────────────────────── */
.number-band {
    background: #f4f6f9; border-radius: 6px;
    padding: 7px 10px; margin-bottom: 10px;
    display: flex; justify-content: space-between; align-items: center;
}
.number-band .recu-num {
    font-size: 13px; font-weight: bold; color: #1a5276; letter-spacing: .03em;
}
.number-band .recu-date { font-size: 9px; color: #7f8c8d; }
.number-band .recu-date strong { color: #1c2833; font-size: 10px; }

/* ── Info élève ───────────────────────────────────── */
.student-section {
    border: 1.5px solid #e4e8ed; border-radius: 6px;
    padding: 8px 10px; margin-bottom: 10px;
}
.student-section .section-title {
    font-size: 7px; text-transform: uppercase;
    letter-spacing: .08em; color: #7f8c8d;
    margin-bottom: 5px; font-weight: bold;
}
.student-name { font-size: 12px; font-weight: bold; color: #1a5276; }
.student-meta { font-size: 8.5px; color: #7f8c8d; margin-top: 2px; }
.student-meta strong { color: #1c2833; }

/* ── Tableau détail paiement ─────────────────────── */
.detail-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.detail-table tr td { padding: 5px 8px; border-bottom: 1px solid #f0f3f6; }
.detail-table tr td:first-child { color: #7f8c8d; font-size: 8.5px; width: 45%; }
.detail-table tr td:last-child  { font-size: 9.5px; font-weight: 600; text-align: right; }
.detail-table .row-total td:first-child {
    font-size: 9px; font-weight: bold; color: #1c2833;
    background: #eafaf1; padding: 7px 8px;
    border-radius: 4px 0 0 4px;
}
.detail-table .row-total td:last-child {
    background: #eafaf1; font-size: 14px; font-weight: bold;
    color: #1e8449; padding: 7px 8px;
    border-radius: 0 4px 4px 0;
}
.detail-table .row-reste td:first-child {
    font-size: 9px; font-weight: bold; color: #1c2833;
}
.detail-table .row-reste td:last-child {
    font-size: 12px; font-weight: bold;
}

/* ── Historique ───────────────────────────────────── */
.historique-title {
    font-size: 7px; text-transform: uppercase;
    letter-spacing: .08em; color: #7f8c8d;
    font-weight: bold; margin-bottom: 5px;
    border-top: 1px dashed #e4e8ed; padding-top: 7px;
}
.histo-table { width: 100%; border-collapse: collapse; }
.histo-table th {
    font-size: 7px; text-transform: uppercase; letter-spacing: .06em;
    color: #7f8c8d; padding: 3px 5px;
    border-bottom: 1px solid #e4e8ed; text-align: left;
}
.histo-table td { font-size: 8.5px; padding: 4px 5px; border-bottom: 1px solid #f8fafc; }
.histo-table .current-row td { font-weight: bold; background: #fef9e7; }
.histo-table td:last-child { text-align: right; }

/* ── Barre de progression ─────────────────────────── */
.progress-section { margin: 8px 0; }
.progress-label {
    display: flex; justify-content: space-between;
    font-size: 8px; color: #7f8c8d; margin-bottom: 3px;
}
.progress-track {
    height: 6px; background: #f0f3f6; border-radius: 999px; overflow: hidden;
}
.progress-fill { height: 100%; border-radius: 999px; }

/* ── Pied de page ─────────────────────────────────── */
.footer {
    border-top: 1.5px solid #1a5276;
    padding-top: 8px; margin-top: 10px;
}
.footer table { width: 100%; }
.footer .cashier { font-size: 8px; color: #7f8c8d; }
.footer .cashier strong { color: #1c2833; }
.footer .sig-label { font-size: 7.5px; color: #7f8c8d; margin-bottom: 4px; }
.footer .sig-box {
    border: 1px dashed #c0c0c0; border-radius: 4px;
    width: 90px; height: 35px; display: inline-block;
}
.footer .stamp {
    font-size: 7px; color: #aaa;
    border: 1px solid #e4e8ed; border-radius: 4px;
    padding: 2px 5px; display: inline-block; margin-top: 3px;
}
.paid-badge {
    font-size: 20px; font-weight: bold;
    color: #1e8449; opacity: .08;
    transform: rotate(-30deg);
    position: absolute; top: 45%; left: 30%;
    white-space: nowrap; letter-spacing: .1em;
    pointer-events: none;
}

/* ── Watermark ANNULÉ ─────────────────────────────── */
.annule-badge {
    font-size: 28px; font-weight: bold;
    color: #e74c3c; opacity: .1;
    transform: rotate(-30deg);
    position: absolute; top: 40%; left: 18%;
    white-space: nowrap; letter-spacing: .15em;
}
</style>
</head>
<body>
<div class="page">

    @if($payment->is_annule)
        <div class="annule-badge">ANNULÉ</div>
    @elseif($payment->montant_restant == 0)
        <div class="paid-badge">SOLDÉ</div>
    @endif

    {{-- ── Bandeau haut ─────────────────── --}}
    <div class="top-band">
        <div>
            <div class="school-name">🏫 École Primaire</div>
            <div class="school-sub">Complexe Scolaire de Saaba</div>
        </div>
        <div class="doc-badge">Reçu de Paiement</div>
    </div>

    {{-- ── Numéro + Date ─────────────────── --}}
    <div class="number-band">
        <div>
            <div class="recu-num">N° {{ $payment->numero_recu }}</div>
        </div>
        <div class="recu-date" style="text-align:right;">
            <div>Date : <strong>{{ $payment->date_paiement->format('d/m/Y') }}</strong></div>
            <div style="margin-top:2px;">Émis le : <strong>{{ now()->format('d/m/Y') }}</strong></div>
        </div>
    </div>

    {{-- ── Élève ─────────────────────────── --}}
    <div class="student-section">
        <div class="section-title">Informations de l'élève</div>
        <table style="width:100%;">
            <tr>
                <td>
                    <div class="student-name">{{ $payment->enrollment->student->nom_complet }}</div>
                    <div class="student-meta">
                        Matricule : <strong>{{ $payment->enrollment->student->matricule }}</strong>
                        &nbsp;·&nbsp; Classe : <strong>{{ $payment->enrollment->schoolClass->nom }}</strong>
                        &nbsp;·&nbsp; Niveau : <strong>{{ $payment->enrollment->schoolClass->niveau }}</strong>
                    </div>
                    @if($payment->enrollment->student->tuteur_nom || $payment->enrollment->student->tuteur_telephone)
                    <div class="student-meta" style="margin-top:2px;">
                        @if($payment->enrollment->student->tuteur_nom)
                            Tuteur : <strong>{{ $payment->enrollment->student->tuteur_nom }}</strong>
                        @endif
                        @if($payment->enrollment->student->tuteur_telephone)
                            &nbsp;· Tél : <strong>{{ $payment->enrollment->student->tuteur_telephone }}</strong>
                        @endif
                    </div>
                    @endif
                </td>
                <td style="width:30%;text-align:right;vertical-align:top;">
                    <div class="student-meta">Année scolaire</div>
                    <div style="font-size:10px;font-weight:bold;color:#1a5276;">
                        {{ $payment->enrollment->academicYear->libelle ?? '—' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── Détail paiement ────────────────── --}}
    <table class="detail-table">
        <tr>
            <td>Type de paiement</td>
            <td>{{ $payment->type_label }}</td>
        </tr>
        <tr>
            <td>Mode de paiement</td>
            <td>{{ $payment->mode_label }}</td>
        </tr>
        @if($payment->reference_externe)
        <tr>
            <td>Référence / Transaction</td>
            <td><span style="font-family:monospace;font-size:9px;">{{ $payment->reference_externe }}</span></td>
        </tr>
        @endif
        <tr>
            <td>Frais de scolarité annuel</td>
            <td>{{ number_format($payment->montant_du, 0, ',', ' ') }} F CFA</td>
        </tr>
        <tr class="row-total">
            <td>&#10003; Montant versé</td>
            <td>{{ number_format($payment->montant_verse, 0, ',', ' ') }} F CFA</td>
        </tr>
        <tr class="row-reste">
            <td>Reste à payer après ce versement</td>
            <td style="color:{{ $payment->montant_restant > 0 ? '#e74c3c' : '#1e8449' }};">
                {{ number_format($payment->montant_restant, 0, ',', ' ') }} F CFA
                @if($payment->montant_restant == 0) &#10003; @endif
            </td>
        </tr>
    </table>

    {{-- ── Barre de progression ─────────────── --}}
    @php
        $frais = $payment->montant_du;
        $totalPaye = $historique->where('is_annule', false)->sum('montant_verse');
        $pct = $frais > 0 ? min(100, round($totalPaye / $frais * 100)) : 100;
        $barColor = $pct >= 100 ? '#1e8449' : ($pct >= 60 ? '#27ae60' : ($pct >= 30 ? '#e67e22' : '#e74c3c'));
    @endphp
    <div class="progress-section">
        <div class="progress-label">
            <span>Avancement du règlement</span>
            <span style="font-weight:bold;color:{{ $barColor }};">{{ $pct }}%</span>
        </div>
        <div class="progress-track">
            <div class="progress-fill" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
        </div>
    </div>

    {{-- ── Historique des versements ─────────── --}}
    @if($historique->count() > 1)
    <div class="historique-title">Historique des versements de l'année</div>
    <table class="histo-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>N° Reçu</th>
                <th>Mode</th>
                <th>Montant</th>
                <th>Solde après</th>
            </tr>
        </thead>
        <tbody>
            @foreach($historique as $h)
            <tr class="{{ $h->id === $payment->id ? 'current-row' : '' }}">
                <td>{{ $h->date_paiement->format('d/m/Y') }}</td>
                <td>{{ $h->numero_recu }}</td>
                <td>{{ $h->mode_label }}</td>
                <td style="color:#1e8449;font-weight:600;">
                    +{{ number_format($h->montant_verse, 0, ',', ' ') }} F
                </td>
                <td style="color:{{ $h->montant_restant > 0 ? '#e74c3c' : '#1e8449' }};">
                    {{ number_format($h->montant_restant, 0, ',', ' ') }} F
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- ── Observations ─────────────────────── --}}
    @if($payment->observations)
    <div style="background:#fef9e7;border-radius:4px;padding:5px 8px;margin-top:8px;font-size:8.5px;">
        <strong>Observations :</strong> {{ $payment->observations }}
    </div>
    @endif

    {{-- ── Pied de page ────────────────────── --}}
    <div class="footer">
        <table>
            <tr>
                <td>
                    <div class="cashier">
                        Encaissé par : <strong>{{ $payment->createdBy?->name ?? '—' }}</strong><br>
                        Généré le : <strong>{{ now()->format('d/m/Y à H\hi') }}</strong>
                    </div>
                    <div class="stamp">Document officiel — Conserver ce reçu</div>
                </td>
                <td style="text-align:right;vertical-align:bottom;">
                    <div class="sig-label">Signature et cachet</div>
                    <div class="sig-box"></div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
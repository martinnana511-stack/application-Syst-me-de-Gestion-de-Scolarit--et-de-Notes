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
    .page { padding: 18px 20px; }

    /* En-tête */
    .header { border-bottom: 3px solid #1a5276; padding-bottom: 10px; margin-bottom: 12px; }
    .header table { width: 100%; }
    .school-name { font-size: 15px; font-weight: bold; color: #1a5276; }
    .school-sub  { font-size: 8px; color: #7f8c8d; text-transform: uppercase; letter-spacing: .05em; }
    .doc-title   {
        background: #1a5276; color: white; text-align: center;
        padding: 5px 12px; border-radius: 4px; font-size: 11px; font-weight: bold;
    }
    .year-badge  {
        background: #eaf2ff; color: #1a5276; padding: 3px 8px;
        border-radius: 4px; font-size: 9px; font-weight: bold; margin-top: 4px;
        display: inline-block;
    }

    /* Infos élève */
    .student-box {
        background: #f4f6f9; border-radius: 6px;
        padding: 8px 12px; margin-bottom: 10px;
    }
    .student-box table { width: 100%; }
    .student-name { font-size: 13px; font-weight: bold; color: #1a5276; }
    .info-label { color: #7f8c8d; font-size: 8.5px; }
    .info-value { font-size: 9.5px; font-weight: bold; }

    /* Tableau des notes */
    .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .grades-table th {
        background: #1a5276; color: white;
        padding: 5px 6px; text-align: left;
        font-size: 8px; text-transform: uppercase; letter-spacing: .04em;
    }
    .grades-table td { padding: 5px 6px; border-bottom: 1px solid #e8ecf0; font-size: 9px; }
    .grades-table tr:nth-child(even) td { background: #f8fafc; }
    .grades-table .note-cell { font-weight: bold; font-size: 10px; text-align: center; }
    .grades-table .absent { color: #e74c3c; font-style: italic; text-align: center; }

    /* Résumé */
    .summary-box {
        background: #1a5276; color: white;
        border-radius: 6px; padding: 10px 14px;
        margin-bottom: 10px;
    }
    .summary-box table { width: 100%; }
    .summary-label { font-size: 8.5px; opacity: .75; }
    .summary-value { font-size: 16px; font-weight: bold; }
    .summary-sub   { font-size: 8px; opacity: .7; }
    .mention-badge {
        background: #e67e22; color: white; padding: 3px 10px;
        border-radius: 999px; font-size: 10px; font-weight: bold;
    }

    /* Pied */
    .footer { border-top: 1px solid #e8ecf0; padding-top: 8px; margin-top: 8px; }
    .signature-box {
        border: 1px dashed #ccc; border-radius: 4px;
        height: 40px; width: 120px;
        display: inline-block;
    }
</style>
</head>
<body>
<div class="page">

    {{-- ── En-tête ───────────────── --}}
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="school-name">🏫 École Primaire</div>
                    <div class="school-sub">Système de Gestion de Scolarité</div>
                </td>
                <td style="text-align:right;">
                    <div class="doc-title">BULLETIN DE NOTES</div>
                    <div style="text-align:right;">
                        <span class="year-badge">{{ $enrollment->academicYear->libelle }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── Infos élève ───────────── --}}
    <div class="student-box">
        <table>
            <tr>
                <td style="width:50%;">
                    <div class="student-name">{{ $enrollment->student->nom_complet }}</div>
                    <div style="margin-top:3px;">
                        <span class="info-label">Matricule : </span>
                        <span class="info-value">{{ $enrollment->student->matricule }}</span>
                    </div>
                </td>
                <td style="width:25%;">
                    <div class="info-label">Classe</div>
                    <div class="info-value">{{ $enrollment->schoolClass->nom }}</div>
                    <div class="info-label" style="margin-top:3px;">Niveau</div>
                    <div class="info-value">{{ $enrollment->schoolClass->niveau }}</div>
                </td>
                <td style="width:25%;text-align:right;">
                    <div class="info-label">Trimestre</div>
                    <div class="info-value">{{ $term->libelle }}</div>
                    <div class="info-label" style="margin-top:3px;">Effectif</div>
                    <div class="info-value">{{ $reportCard->effectif_classe ?? '—' }} élèves</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── Tableau des notes ─────── --}}
    <table class="grades-table">
        <thead>
            <tr>
                <th style="width:35%;">Matière</th>
                <th style="width:10%;text-align:center;">Coeff.</th>
                <th style="width:15%;text-align:center;">Note</th>
                <th style="width:15%;text-align:center;">Moy. pond.</th>
                <th style="width:15%;text-align:center;">Mention</th>
                <th>Appréciation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $subject)
            @php
                $g      = $grades->get($subject->id);
                $noteSur20 = $g ? $g->note_normalisee : null;
                $pond   = $noteSur20 !== null ? round($noteSur20 * $subject->coefficient, 2) : null;
            @endphp
            <tr>
                <td style="font-weight:600;">{{ $subject->nom }}</td>
                <td style="text-align:center;color:#7f8c8d;">{{ $subject->coefficient }}</td>
                <td class="{{ $g?->is_absent ? 'absent' : 'note-cell' }}"
                    style="color:{{ $noteSur20 !== null ? ($noteSur20 >= 10 ? '#1e8449' : '#c0392b') : '#888' }};">
                    @if($g?->is_absent)
                        ABS
                    @elseif($noteSur20 !== null)
                        {{ number_format($noteSur20, 2) }}
                    @else
                        —
                    @endif
                </td>
                <td style="text-align:center;color:#555;">
                    {{ $pond !== null ? number_format($pond, 2) : '—' }}
                </td>
                <td style="text-align:center;">
                    @if($g && !$g->is_absent)
                        <span style="
                            background:{{ $noteSur20 >= 14 ? '#eafaf1' : ($noteSur20 >= 10 ? '#fef9e7' : '#fdedec') }};
                            color:{{ $noteSur20 >= 14 ? '#1e8449' : ($noteSur20 >= 10 ? '#d68910' : '#c0392b') }};
                            padding:2px 5px; border-radius:3px; font-size:8px;">
                            {{ $g->mention }}
                        </span>
                    @else
                        <span style="color:#aaa;">—</span>
                    @endif
                </td>
                <td style="color:#555;font-style:italic;">{{ $g?->appreciation ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Résumé ────────────────── --}}
    <div class="summary-box">
        <table>
            <tr>
                <td style="width:25%;">
                    <div class="summary-label">MOYENNE GÉNÉRALE</div>
                    <div class="summary-value">
                        {{ $reportCard->moyenne_generale !== null
                            ? number_format($reportCard->moyenne_generale, 2)
                            : '—' }}<span style="font-size:10px;">/20</span>
                    </div>
                </td>
                <td style="width:20%;">
                    <div class="summary-label">RANG</div>
                    <div class="summary-value">
                        {{ $reportCard->rang ?? '—' }}<span style="font-size:10px;">/ {{ $reportCard->effectif_classe ?? '—' }}</span>
                    </div>
                </td>
                <td style="width:30%;text-align:center;">
                    @if($reportCard->mention)
                        <span class="mention-badge">{{ $reportCard->mention }}</span>
                    @endif
                </td>
                <td style="text-align:right;color:rgba(255,255,255,.7);font-size:8.5px;">
                    Calculé le {{ $reportCard->calculated_at?->format('d/m/Y') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Appréciation du conseil --}}
    @if($reportCard->appreciation_conseil)
    <div style="background:#f4f6f9;border-radius:5px;padding:7px 10px;margin-bottom:10px;font-size:9px;">
        <strong>Appréciation du conseil de classe :</strong>
        {{ $reportCard->appreciation_conseil }}
    </div>
    @endif

    {{-- ── Pied de page ─────────── --}}
    <div class="footer">
        <table style="width:100%;">
            <tr>
                <td>
                    <div style="font-size:8px;color:#aaa;">
                        Document généré le {{ now()->format('d/m/Y à H:i') }} — SGS École Primaire
                    </div>
                </td>
                <td style="text-align:right;">
                    <div style="font-size:8px;color:#7f8c8d;margin-bottom:3px;">
                        Signature et cachet de la direction
                    </div>
                    <div class="signature-box"></div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>

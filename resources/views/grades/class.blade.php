@extends('layouts.app')
@section('title', 'Notes — ' . $class->nom)
@section('page-title', 'Saisie des notes — ' . $class->nom . ' · ' . $term->libelle)
@section('breadcrumb')
    / <a href="{{ route('grades.index') }}">Notes</a>
    / {{ $class->nom }} / {{ $term->libelle }}
@endsection

@section('content')

{{-- ── En-tête actions ──────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex gap-2 align-items-center">
        <span class="badge" style="background:#eaf2ff;color:var(--sgs-primary);font-size:.8rem;padding:.4rem .8rem;">
            {{ $enrollments->count() }} élève(s)
        </span>
        @if($term->is_closed)
            <span class="badge bg-danger-subtle text-danger">
                <i class="bi bi-lock me-1"></i>Trimestre clôturé
            </span>
        @else
            <span class="badge bg-success-subtle text-success">
                <i class="bi bi-unlock me-1"></i>Saisie ouverte
            </span>
        @endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('grades.export', [$class, $term]) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-download me-1"></i>Export CSV
        </a>
        <a href="{{ route('report-cards.index') }}?class_id={{ $class->id }}&amp;term_id={{ $term->id }}"
           class="btn btn-outline-primary btn-sm">
            <i class="bi bi-list-ol me-1"></i>Classement
        </a>
        @if(!$term->is_closed)
        <button id="btn-save-all" class="btn btn-primary btn-sm" onclick="saveAllGrades()">
            <i class="bi bi-floppy me-1"></i>Tout enregistrer
        </button>
        @endif
    </div>
</div>

{{-- ── Sélecteur de matière ─────────────────────────── --}}
<div class="card mb-3">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="small fw-semibold text-muted text-uppercase" style="letter-spacing:.07em;">Matière :</span>
            @foreach($subjects as $sub)
            <button class="btn btn-sm subject-btn {{ $loop->first ? 'btn-primary' : 'btn-outline-secondary' }}"
                    data-subject="{{ $sub->id }}"
                    data-coeff="{{ $sub->coefficient }}"
                    data-note-max="{{ $sub->note_max}}"
                    onclick="selectSubject({{ $sub->id }}, this)">
                {{ $sub->nom }}
                <span class="badge ms-1" style="background:rgba(255,255,255,.25);font-size:.65rem;">
                    ×{{ $sub->coefficient }}
                </span>
            </button>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Grille des notes ─────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span id="subject-title">
            <i class="bi bi-pencil-square me-2"></i>
            <span id="subject-name">{{ $subjects->first()->nom ?? 'Matière' }}</span>
        </span>
        <span class="text-muted small" id="save-status"></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="grades-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Élève</th>
                        <th style="width:60px;">Sexe</th>
                        <th style="width:120px;">Note</th>
                        <th style="width:90px;">Absent</th>
                        <th>Appréciation</th>
                        <th style="width:80px;">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $i => $enrollment)
                    @php
                        $key = "{$enrollment->id}_" . ($subjects->first()->id ?? 0);
                        $g   = $existingGrades->get($key);
                    @endphp
                    <tr data-enrollment="{{ $enrollment->id }}" class="grade-row">
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $enrollment->student->nom_complet }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $enrollment->student->matricule }}</div>
                        </td>
                        <td>
                            <span style="font-size:.8rem;">
                                {{ $enrollment->student->sexe === 'M' ? '♂' : '♀' }}
                            </span>
                        </td>
                        <td>
                            <input type="number"
                                   class="form-control form-control-sm note-input"
                                   value="{{ $g && !$g->is_absent ? $g->note : '' }}"
                                   min="0" max="{{ $subjects->first()->note_max }}" step="0.25"
                                   placeholder="—"
                                   {{ $term->is_closed ? 'disabled' : '' }}
                                   onchange="markChanged(this)">
                        </td>
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input absent-check" type="checkbox"
                                       {{ $g?->is_absent ? 'checked' : '' }}
                                       {{ $term->is_closed ? 'disabled' : '' }}
                                       onchange="toggleAbsent(this)">
                            </div>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm appreciation-input"
                                   value="{{ $g?->appreciation ?? '' }}"
                                   placeholder="Commentaire…"
                                   {{ $term->is_closed ? 'disabled' : '' }}>
                        </td>
                        <td class="text-center">
                            <span class="row-status">
                                @if($g)
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                @else
                                    <i class="bi bi-circle text-muted"></i>
                                @endif
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentSubjectId = {{ $subjects->first()->id ?? 0 }};
const classId        = {{ $class->id }};
const termId         = {{ $term->id }};
const isClosed       = {{ $term->is_closed ? 'true' : 'false' }};

// Données des notes existantes pour toutes les matières
const existingGrades = @json($existingGrades->toArray());

function selectSubject(subjectId, btn) {
    currentSubjectId = subjectId;
    const noteMax = parseFloat(btn.dataset.noteMax ?? 20);

    // Mettre à jour le max de tous les inputs
    document.querySelectorAll('.subject-btn').forEach(b => {
        b.classList.remove('btn-primary');
        b.classList.add('btn-outline-secondary');
    });
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-primary');

    document.getElementById('subject-name').textContent = btn.textContent.trim().split('\n')[0].trim();

    // Mettre a jours le max de tous les inputs de note
    document.querySelectorAll('.note-input').forEach(input => {
        input.max = noteMax;
        input.placeholder = `0-${noteMax}`;
    });

    // Mettre à jour les notes affichées dans la grille
    document.querySelectorAll('.grade-row').forEach(row => {
        const enrollmentId = row.dataset.enrollment;
        const key = `${enrollmentId}_${subjectId}`;
        const grade = existingGrades[key];

        row.querySelector('.note-input').value = grade && !grade.is_absent ? grade.note : '';
        row.querySelector('.absent-check').checked = grade?.is_absent ?? false;
        row.querySelector('.appreciation-input').value = grade?.appreciation ?? '';
        row.querySelector('.row-status').innerHTML = grade
            ? '<i class="bi bi-check-circle-fill text-success"></i>'
            : '<i class="bi bi-circle text-muted"></i>';

        toggleAbsent(row.querySelector('.absent-check'));
    });
}

function toggleAbsent(checkbox) {
    const noteInput = checkbox.closest('tr').querySelector('.note-input');
    noteInput.disabled = checkbox.checked || isClosed;
    if (checkbox.checked) noteInput.value = '';
}

function markChanged(input) {
    input.closest('tr').querySelector('.row-status').innerHTML =
        '<i class="bi bi-pencil text-warning"></i>';
}

async function saveAllGrades() {
    // Récupérer la note max de la matière courante
    // const noteMax = {{ $subjects->first()->note_max ?? 20}};
    const activeBtn = document.querySelector('.subject-btn.btn-primary');
    const noteMax   = parseFloat(activeBtn?.dataset.noteMax ?? 20);
    const nomMatiere = activeBtn?.textContent.trim().split('\n')[0].trim() ?? '';

    // Vérifier si la cases sont vide sans absence cochée
    let hasEmpty = false;
        document.querySelectorAll('.grade-row').forEach(row => {
            const noteInput   = row.querySelector('.note-input');
            const absentCheck = row.querySelector('.absent-check');

            // Vérifier seulement si pas absent et si une note est saisie
            if (!absentCheck.checked && noteInput.value === '') {
                    noteInput.style.border      = '2px solid orange';
                    noteInput.style.borderRadius = '4px';
                    noteInput.style.background   = '#fef9e7';
                    hasEmpty = true;
            }
        });

    if (hasEmpty) {
        document.getElementById('save-status').innerHTML =
            `<span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Des cases sont vides ! Saisissez une note ou cochez Absent.</span>`;
        setTimeout(() => {
            document.getElementById('save-status').innerHTML = '';

            document.querySelectorAll('.note-input').forEach(input => {
                input.style.border = '';
                input.style.background = '';
            });
        },5000);
        return;
    }

    // Vérifier si les notes saisies sont valides
    let hasError = false;
        document.querySelectorAll('.grade-row').forEach(row => {
            const noteInput   = row.querySelector('.note-input');
            const absentCheck = row.querySelector('.absent-check');

            // Réinitialiser d'abord
            noteInput.style.border     = '';
            noteInput.style.background = '';

            // Vérifier seulement si pas absent et si une note est saisie
            if (!absentCheck.checked && noteInput.value !== '') {
                const val = parseFloat(noteInput.value);
                if (val < 0 || val > noteMax) {
                    noteInput.style.border      = '2px solid red';
                    noteInput.style.borderRadius = '4px';
                    noteInput.style.background   = '#fdedec';
                    hasError = true;
                }
            }
        });

    if (hasError) {
        document.getElementById('save-status').innerHTML =
            `<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Notes invalides ! Les notes doivent être comprise entre 0 et ${noteMax} pour la matière ${nomMatiere}.</span>`;
        setTimeout(() => {
            document.getElementById('save-status').innerHTML = '';

            document.querySelectorAll('.note-input').forEach(input => {
                input.style.border = '';
                input.style.background = '';
            });
        },5000);
        return;
    }

    const btn = document.getElementById('btn-save-all');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enregistrement…';

    const notes = [];
    document.querySelectorAll('.grade-row').forEach(row => {
        const noteInput   = row.querySelector('.note-input');
        const absentCheck = row.querySelector('.absent-check');
        const apprInput   = row.querySelector('.appreciation-input');
        notes.push({
            enrollment_id: row.dataset.enrollment,
            note:          noteInput.value || null,
            is_absent:     absentCheck.checked ? 1 : 0,
            appreciation:  apprInput.value || null,
        });
    });

    try {
        const res = await fetch('{{ route("grades.bulk") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ term_id: termId, class_id: classId, subject_id: currentSubjectId, notes }),
        });

        const data = await res.json();

        if (data.success) {
            document.querySelectorAll('.row-status').forEach(s => {
                s.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
            });
            document.getElementById('save-status').innerHTML =
                `<i class="bi bi-check-circle text-success me-1"></i>${data.message}`;

            // Rafraîchir les données locales
            notes.forEach(n => {
                const key = `${n.enrollment_id}_${currentSubjectId}`;
                existingGrades[key] = {
                    note: n.note, is_absent: n.is_absent, appreciation: n.appreciation
                };
            });
        } else {
            document.getElementById('save-status').innerHTML =
                `<span class="text-danger"><i class="bi bi-x-circle me-1"></i>${data.error ?? 'Erreur'}</span>`;
        }
    } catch (e) {
        document.getElementById('save-status').innerHTML =
            '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Erreur réseau</span>';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-floppy me-1"></i>Tout enregistrer';
        setTimeout(() => document.getElementById('save-status').innerHTML = '', 5000);
    }
}


// Init : désactiver les inputs si absent coché
    document.querySelectorAll('.absent-check').forEach(toggleAbsent);

</script>
@endpush

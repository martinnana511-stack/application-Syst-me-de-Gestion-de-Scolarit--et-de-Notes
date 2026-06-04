{{-- ═══════════════════════════════════════════════════════════
     resources/views/payments/create.blade.php
════════════════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('title', 'Enregistrer un paiement')
@section('page-title', 'Nouveau paiement')
@section('breadcrumb')
    / <a href="{{ route('payments.index') }}">Paiements</a> / Nouveau
@endsection

@section('content')

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-cash-coin me-2"></i>Enregistrer un versement</div>
            <div class="card-body">

                <form method="POST" action="{{ route('payments.store') }}" id="payment-form">
                    @csrf

                    {{-- Sélection de l'élève --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Élève <span class="text-danger">*</span></label>
                        <select name="enrollment_id" id="enrollment-select"
                                class="form-select @error('enrollment_id') is-invalid @enderror" required>
                            <option value="">— Rechercher un élève —</option>
                            <!-- @if($student)
                                <option value="{{ $student->currentEnrollment->id }}" selected>
                                    {{ $student->nom_complet }} — {{ $student->currentEnrollment->schoolClass->nom }}
                                </option>
                            @endif -->
                            @if($student && $student->currentEnrollment)
                                <option value="{{ $student->currentEnrollment->id }}" selected>
                                    {{ $student->nom_complet }} — {{ $student->currentEnrollment->schoolClass->nom ?? '—' }}
                                </option>
                            @elseif($student && !$student->currentEnrollment)
                                <option value="" disabled selected>
                                    {{ $student->nom_complet }} — Aucune inscription active
                                </option>
                            @endif
                        </select>
                        @error('enrollment_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Résumé financier (affiché dynamiquement) --}}
                    <div id="financial-summary" class="alert alert-info py-2 small mb-3 {{ $student ? '' : 'd-none' }}">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="fw-bold" id="sum-total">
                                    {{ $student ? number_format($student->currentEnrollment->schoolClass->frais_scolarite_annuel, 0, ',', ' ') . ' F' : '—' }}
                                </div>
                                <div class="text-muted">Total dû</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-success" id="sum-paye">
                                    {{ $student ? number_format($student->totalPaye(), 0, ',', ' ') . ' F' : '—' }}
                                </div>
                                <div class="text-muted">Déjà payé</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-danger" id="sum-reste">
                                    {{ $student ? number_format($student->resteAPayer(), 0, ',', ' ') . ' F' : '—' }}
                                </div>
                                <div class="text-muted">Reste à payer</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Type de paiement <span class="text-danger">*</span></label>
                            <select name="type_paiement" class="form-select form-select-sm" required>
                                <option value="scolarite" @selected(old('type_paiement','scolarite')==='scolarite')>Frais de scolarité</option>
                                <option value="inscription" @selected(old('type_paiement')==='inscription')>Frais d'inscription</option>
                                <option value="autre" @selected(old('type_paiement')==='autre')>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Mode de paiement <span class="text-danger">*</span></label>
                            <select name="mode_paiement" class="form-select form-select-sm" required>
                                <option value="especes" @selected(old('mode_paiement','especes')==='especes')>💵 Espèces</option>
                                <option value="mobile_money" @selected(old('mode_paiement')==='mobile_money')>📱 Mobile Money</option>
                                <option value="cheque" @selected(old('mode_paiement')==='cheque')>🏦 Chèque</option>
                                <option value="virement" @selected(old('mode_paiement')==='virement')>🔁 Virement</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Montant versé (F CFA) <span class="text-danger">*</span></label>
                            <input type="number" name="montant_verse" id="montant-input"
                                   class="form-control form-control-sm @error('montant_verse') is-invalid @enderror"
                                   value="{{ old('montant_verse') }}"
                                   min="100" step="100" placeholder="Ex : 30000" required>
                            @error('montant_verse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small">Date du paiement <span class="text-danger">*</span></label>
                            <input type="date" name="date_paiement"
                                   class="form-control form-control-sm @error('date_paiement') is-invalid @enderror"
                                   value="{{ old('date_paiement', date('Y-m-d')) }}" required>
                            @error('date_paiement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium small">Référence externe (Mobile Money, chèque…)</label>
                            <input type="text" name="reference_externe" class="form-control form-control-sm"
                                   value="{{ old('reference_externe') }}" placeholder="Numéro de transaction, chèque…">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium small">Observations</label>
                            <textarea name="observations" class="form-control form-control-sm" rows="2"
                                      placeholder="Notes optionnelles…">{{ old('observations') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-check2 me-1"></i>Enregistrer et générer le reçu
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Aide --}}
    <div class="col-lg-5">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>À savoir</h6>
                <ul class="small text-muted ps-3">
                    <li>Un <strong>reçu PDF</strong> est généré automatiquement après chaque paiement.</li>
                    <li>Le montant versé ne peut pas dépasser le <strong>reste à payer</strong>.</li>
                    <li>Chaque paiement est <strong>archivé</strong> et consultable dans l'historique de l'élève.</li>
                    <li>En cas d'erreur, un gestionnaire peut <strong>annuler</strong> un paiement avec un motif.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Recherche AJAX d'élèves dans le select
document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('enrollment-select');
    if (!select) return;

    // Initialisation simple — dans un vrai projet utiliser Select2 ou Tom Select
    select.addEventListener('change', function() {
        const enrollmentId = this.value;
        if (!enrollmentId) {
            document.getElementById('financial-summary').classList.add('d-none');
            return;
        }
        // AJAX pour récupérer les infos financières de l'élève
        fetch(`/api/enrollments/${enrollmentId}/summary`, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('sum-total').textContent = data.frais.toLocaleString('fr-FR') + ' F';
            document.getElementById('sum-paye').textContent = data.paye.toLocaleString('fr-FR') + ' F';
            document.getElementById('sum-reste').textContent = data.reste.toLocaleString('fr-FR') + ' F';
            document.getElementById('financial-summary').classList.remove('d-none');
            document.getElementById('montant-input').max = data.reste;
        })
        .catch(() => {});
    });
});
</script>
@endpush

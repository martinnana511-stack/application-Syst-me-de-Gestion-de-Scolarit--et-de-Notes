<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'numero_recu',
        'type_paiement',
        'montant_verse',
        'montant_du',
        'montant_restant',
        'mode_paiement',
        'reference_externe',
        'date_paiement',
        'observations',
        'pdf_path',
        'created_by',
        'is_annule',
        'motif_annulation',
    ];

    protected function casts(): array
    {
        return [
            'montant_verse'   => 'decimal:2',
            'montant_du'      => 'decimal:2',
            'montant_restant' => 'decimal:2',
            'date_paiement'   => 'date',
            'is_annule'       => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Hooks
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->numero_recu)) {
                $payment->numero_recu = static::generateNumeroRecu();
            }
        });

        static::created(function (Payment $payment) {
            // Mettre à jour le montant restant après chaque paiement
            $payment->updateMontantRestant();
        });
    }

    // -------------------------------------------------------------------------
    // Génération du numéro de reçu
    // -------------------------------------------------------------------------

    public static function generateNumeroRecu(): string
    {
        $year   = date('Y');
        $prefix = 'REC-' . $year . '-';
        $last   = static::where('numero_recu', 'like', $prefix . '%')
                        ->orderByDesc('numero_recu')
                        ->value('numero_recu');

        $num = $last ? (intval(substr($last, -6)) + 1) : 1;
        return $prefix . str_pad($num, 6, '0', STR_PAD_LEFT);
    }

    // -------------------------------------------------------------------------
    // Méthodes métier
    // -------------------------------------------------------------------------

    /**
     * Recalcule et met à jour le montant restant en tenant compte
     * de tous les paiements précédents non annulés.
     */
    public function updateMontantRestant(): void
    {
        $frais = $this->enrollment?->schoolClass?->frais_scolarite_annuel ?? $this->montant_du;

        $totalPaye = static::where('enrollment_id', $this->enrollment_id)
            ->where('is_annule', false)
            ->sum('montant_verse');

        $this->updateQuietly(['montant_restant' => max(0, $frais - $totalPaye)]);
    }

    /** URL du reçu PDF */
    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
    }

    /** Label lisible du mode de paiement */
    public function getModeLabelAttribute(): string
    {
        return match($this->mode_paiement) {
            'especes'      => 'Espèces',
            'cheque'       => 'Chèque',
            'mobile_money' => 'Mobile Money',
            'virement'     => 'Virement',
            default        => ucfirst($this->mode_paiement),
        };
    }

    /** Label lisible du type de paiement */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type_paiement) {
            'inscription' => 'Frais d\'inscription',
            'scolarite'   => 'Frais de scolarité',
            'autre'       => 'Autre',
            default       => ucfirst($this->type_paiement),
        };
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeValides($query)
    {
        return $query->where('is_annule', false);
    }

    public function scopeForCurrentYear($query)
    {
        return $query->whereHas('enrollment.academicYear', fn($q) => $q->where('is_active', true));
    }

    public function scopeBetweenDates($query, string $from, string $to)
    {
        return $query->whereBetween('date_paiement', [$from, $to]);
    }
}

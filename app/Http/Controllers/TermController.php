<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TermController extends Controller
{
    /** Formulaire de création d'un trimestre */
    public function create(AcademicYear $year): View
    {
        return view('settings.terms.create', compact('year'));
    }

    /** Enregistrement d'un trimestre */
    public function store(Request $request, AcademicYear $year): RedirectResponse
    {
        $data = $request->validate([
            'numero'     => 'required|in:1,2,3',
            'libelle'    => 'required|string|max:30',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ]);

        // Vérifier qu'un trimestre avec ce numéro n'existe pas déjà
        $exists = Term::where('academic_year_id', $year->id)
                      ->where('numero', $data['numero'])
                      ->exists();

        if ($exists) {
            return back()->withErrors([
                'numero' => "Le trimestre {$data['numero']} existe déjà pour cette année."
            ]);
        }

        Term::create([
            ...$data,
            'academic_year_id' => $year->id,
            'is_closed'        => false,
        ]);

        return redirect()
            ->route('settings.academic-years.index')
            ->with('success', "{$data['libelle']} créé avec succès.");
    }

    /** Formulaire de modification */
    public function edit(Term $term): View
    {
        return view('settings.terms.edit', compact('term'));
    }

    /** Mise à jour */
    public function update(Request $request, Term $term): RedirectResponse
    {
        $data = $request->validate([
            'libelle'    => 'required|string|max:30',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ]);

        $term->update($data);

        return redirect()
            ->route('settings.academic-years.index')
            ->with('success', 'Trimestre mis à jour.');
    }

    /** Clôturer un trimestre */
    public function close(Term $term): RedirectResponse
    {
        $term->update(['is_closed' => true]);

        return back()->with('success', "{$term->libelle} clôturé. Les notes ne peuvent plus être modifiées.");
    }

    /** Réouvrir un trimestre */
    public function reopen(Term $term): RedirectResponse
    {
        $term->update(['is_closed' => false]);

        return back()->with('success', "{$term->libelle} réouvert. Les notes peuvent à nouveau être modifiées.");
    }

    /** Supprimer un trimestre */
    public function destroy(Term $term): RedirectResponse
    {
        if ($term->grades()->count() > 0) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer un trimestre avec des notes enregistrées.'
            ]);
        }

        $term->delete();

        return back()->with('success', 'Trimestre supprimé.');
    }
}
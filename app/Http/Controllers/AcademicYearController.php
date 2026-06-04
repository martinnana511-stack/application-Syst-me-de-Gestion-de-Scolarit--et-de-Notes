<?php

// =============================================================================
// AcademicYearController
// =============================================================================

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $years = AcademicYear::orderByDesc('date_debut')
            ->with(['terms' => fn($q) => $q->orderBy('numero')])
            ->withCount('classes', 'enrollments')
            ->get();
        return view('settings.academic-years.index', compact('years'));
    }

    public function create(): View
    {
        return view('settings.academic-years.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'libelle'    => 'required|string|max:20|unique:academic_years,libelle',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ]);

        AcademicYear::create($data + ['is_active' => false]);

        return redirect()
            ->route('settings.academic-years.index')
            ->with('success', "Année scolaire {$data['libelle']} créée.");
    }

    public function edit(AcademicYear $academicYear): View
    {
        return view('settings.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $data = $request->validate([
            'libelle'    => "required|string|max:20|unique:academic_years,libelle,{$academicYear->id}",
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ]);

        $academicYear->update($data);

        return redirect()
            ->route('settings.academic-years.index')
            ->with('success', 'Année scolaire mise à jour.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        if ($academicYear->is_active) {
            return back()->withErrors(['error' => 'Impossible de supprimer l\'année scolaire active.']);
        }
        $academicYear->delete();
        return redirect()->route('settings.academic-years.index')->with('success', 'Année supprimée.');
    }

    /** Rend cette année scolaire active (désactive les autres) */
    public function activate(AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->activate();
        return back()->with('success', "Année {$academicYear->libelle} définie comme année active.");
    }
}

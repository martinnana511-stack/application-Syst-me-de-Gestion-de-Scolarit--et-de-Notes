<?php


// =============================================================================
// SubjectController
// =============================================================================

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{

    public function index(): View
    {
        $subjects = Subject::orderBy('nom')->paginate(20);
        return view('settings.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        return view('settings.subjects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:100',
            'code'        => 'required|string|max:10|unique:subjects,code',
            'coefficient' => 'required|numeric|min:0.5|max:10',
            'note_max'    => 'required|integer|in:10,20,100',
        ]);

        Subject::create($data + ['is_active' => true]);

        return redirect()
            ->route('settings.subjects.index')
            ->with('success', "Matière {$data['nom']} créée.");
    }

    public function edit(Subject $subject): View
    {
        return view('settings.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:100',
            'code'        => "required|string|max:10|unique:subjects,code,{$subject->id}",
            'coefficient' => 'required|numeric|min:0.5|max:10',
            'note_max'    => 'required|integer|in:10,20,100',
            'is_active'   => 'boolean',
        ]);

        $subject->update($data);
        return redirect()->route('settings.subjects.index')->with('success', 'Matière mise à jour.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        if ($subject->grades()->count() > 0) {
            return back()->withErrors(['error' => 'Impossible de supprimer une matière avec des notes existantes.']);
        }
        $subject->delete();
        return redirect()->route('settings.subjects.index')->with('success', 'Matière supprimée.');
    }
}

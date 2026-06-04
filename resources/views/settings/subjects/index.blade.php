@extends('layouts.app')
@section('title', 'Matières')
@section('page-title', 'Gestion des matières')
@section('breadcrumb') / Paramètres / Matières@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0 small"><strong>{{ $subjects->total() }}</strong> matière(s)</p>
    <a href="{{ route('settings.subjects.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Nouvelle matière
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Code</th>
                    <th>Coefficient</th>
                    <th>Note max</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                <tr>
                    <td class="fw-semibold">{{ $subject->nom }}</td>
                    <td><code>{{ $subject->code }}</code></td>
                    <td>{{ $subject->coefficient }}</td>
                    <td>{{ $subject->note_max }}</td>
                    <td>
                        @if($subject->is_active)
                            <span class="badge bg-success-subtle text-success">Active</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('settings.subjects.edit', $subject) }}"
                               class="btn btn-xs btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('settings.subjects.destroy', $subject) }}"
                                  onsubmit="return confirm('Supprimer cette matière ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Aucune matière.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subjects->hasPages())
    <div class="card-footer bg-white">{{ $subjects->links('pagination::bootstrap-5') }}</div>
    @endif
</div>

@endsection
@push('styles')
<style>.btn-xs { padding:.2rem .45rem; font-size:.75rem; }</style>
@endpush
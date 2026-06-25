@extends('layouts.app')
@section('title', 'Bulletins')
@section('page-title', 'Bulletins de notes')
@section('breadcrumb') / <a href="#">Bulletins</a>@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <i class="bi bi-file-earmark-text me-2"></i>Sélectionner une classe et un trimestre
    </div>
    <div class="card-body">
        <form id="report-select-form">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-medium small">Classe</label>
                    <select id="class-select" class="form-select" required>
                        <option value="">— Choisir une classe —</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->nom }} ({{ $c->niveau }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-medium small">Trimestre</label>
                    <select id="term-select" class="form-select" required>
                        <option value="">— Choisir un trimestre —</option>
                        @foreach($terms as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->libelle }}
                                {{ $t->is_closed ? '(clôturé)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-right me-1"></i>Ouvrir
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>

document.getElementById('report-select-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const classId = document.getElementById('class-select').value;
    const termId  = document.getElementById('term-select').value;
    if (!classId || !termId) {
        alert('Veuillez sélectionner une classe et un trimestre.');
        return;
    }
    window.location = `/report-cards/show?class_id=${classId}&term_id=${termId}`;
});
</script>
@endpush
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TermController;

// =============================================================================
// Routes publiques
// =============================================================================

Route::get('/', fn() => redirect()->route('login'));

// =============================================================================
// Authentification
// =============================================================================

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// =============================================================================
// Routes protégées — utilisateur authentifié + compte actif
// =============================================================================

Route::middleware(['auth', 'active'])->group(function () {

    // -------------------------------------------------------------------------
    // Tableau de bord — gestionnaire uniquement
    // -------------------------------------------------------------------------
    Route::middleware('role:gestionnaire')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard.index');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
            ->name('dashboard.stats');
        Route::get('/dashboard/impayes', [DashboardController::class, 'impayes'])
            ->name('dashboard.impayes');
        Route::get('report-cards/show', [ReportCardController::class, 'show'])
            ->name('report-cards.show');
    });

    // -------------------------------------------------------------------------
    // Gestion des élèves — gestionnaire uniquement
    // -------------------------------------------------------------------------
    Route::middleware('role:gestionnaire')->group(function () {
        Route::resource('students', StudentController::class);
        Route::get('students/{student}/card', [StudentController::class, 'card'])
            ->name('students.card');
        Route::post('students/{student}/toggle-active', [StudentController::class, 'toggleActive'])
            ->name('students.toggle-active');
    });

    // -------------------------------------------------------------------------
    // Inscriptions — gestionnaire uniquement
    // -------------------------------------------------------------------------
    Route::middleware('role:gestionnaire')->group(function () {
        Route::resource('enrollments', EnrollmentController::class)->except(['edit', 'update']);
        Route::patch('enrollments/{enrollment}/status', [EnrollmentController::class, 'updateStatus'])
            ->name('enrollments.status');
    });

    // -------------------------------------------------------------------------
    // Paiements — gestionnaire uniquement
    // -------------------------------------------------------------------------
    Route::middleware('role:gestionnaire')->group(function () {
        Route::resource('payments', PaymentController::class)->except(['edit', 'update', 'destroy']);
        Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])
            ->name('payments.receipt');
        Route::get('payments/{payment}/pdf', [PaymentController::class, 'pdf'])
            ->name('payments.pdf');
        Route::patch('payments/{payment}/cancel', [PaymentController::class, 'cancel'])
            ->name('payments.cancel');
        Route::get('students/{student}/payments', [PaymentController::class, 'studentPayments'])
            ->name('payments.student');
    });

    // -------------------------------------------------------------------------
    // Notes — gestionnaire + enseignant
    // -------------------------------------------------------------------------
    Route::middleware('role:gestionnaire,enseignant')->group(function () {
        Route::resource('grades', GradeController::class)->except(['show']);
        Route::get('classes/{class}/grades/{term}', [GradeController::class, 'classGrades'])
            ->name('grades.class');
        Route::post('grades/bulk', [GradeController::class, 'bulkStore'])
            ->name('grades.bulk');
        Route::get('grades/export/{class}/{term}', [GradeController::class, 'export'])
            ->name('grades.export');
    });

    // -------------------------------------------------------------------------
    // Bulletins et moyennes — gestionnaire + enseignant
    // -------------------------------------------------------------------------
    Route::middleware('role:gestionnaire,enseignant')->group(function () {
        Route::get('report-cards', [ReportCardController::class, 'index'])
            ->name('report-cards.index');
        Route::get('report-cards/show', [ReportCardController::class, 'show'])
            ->name('report-cards.show');
        Route::get('report-cards/{enrollment}/{term}/pdf', [ReportCardController::class, 'pdf'])
            ->name('report-cards.pdf');
        Route::post('report-cards/{class}/{term}/recalculate', [ReportCardController::class, 'recalculate'])
            ->name('report-cards.recalculate');
    });

    Route::middleware('role:gestionnaire')->group(function () {
        Route::patch('report-cards/{enrollment}/{term}/publish', [ReportCardController::class, 'publish'])
            ->name('report-cards.publish');
    });

    // -------------------------------------------------------------------------
    // Paramétrage — gestionnaire uniquement
    // -------------------------------------------------------------------------
    Route::middleware('role:gestionnaire')->prefix('settings')->name('settings.')->group(function () {

        // Année scolaire
        Route::resource('academic-years', AcademicYearController::class);
        Route::patch('academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate'])
            ->name('academic-years.activate');

        // Trimestre
        Route::get('academic-years/{year}/terms/create', [TermController::class, 'create'])
            ->name('terms.create');
        Route::post('academic-years/{year}/terms', [TermController::class, 'store'])
            ->name('terms.store');
        Route::get('terms/{term}/edit', [TermController::class, 'edit'])
            ->name('terms.edit');
        Route::put('terms/{term}', [TermController::class, 'update'])
            ->name('terms.update');
        Route::patch('terms/{term}/close', [TermController::class, 'close'])
            ->name('terms.close');
        Route::patch('terms/{term}/reopen', [TermController::class, 'reopen'])
            ->name('terms.reopen');
        Route::delete('terms/{term}', [TermController::class, 'destroy'])
            ->name('terms.destroy');


        // Classes
        Route::resource('classes', SchoolClassController::class)
            ->parameters(['classes' => 'schoolClass']);
        Route::post('classes/{class}/subjects/sync', [SchoolClassController::class, 'syncSubjects'])
            ->name('classes.subjects.sync');

        // Matières
        Route::resource('subjects', SubjectController::class);

        // Utilisateurs (comptes admin/enseignants)
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
        Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->name('users.reset-password');
    });
});

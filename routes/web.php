<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomingDocumentController;
use App\Http\Controllers\OutgoingDocumentController;
use App\Http\Controllers\CommonDocumentController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AttachmentTreeController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'protocolYear'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Create documents
    Route::get('/documents/create', function () {
        return view('documents.create');
    })->name('documents.create');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Incoming documents
    Route::get('/incoming', [IncomingDocumentController::class, 'index'])->name('incoming.index');
    Route::post('/incoming/store', [IncomingDocumentController::class, 'store'])->name('incoming.store');
    Route::get('/incoming/{id}/edit', [IncomingDocumentController::class, 'edit'])->name('incoming.edit');
    Route::put('/incoming/{id}', [IncomingDocumentController::class, 'update'])->name('incoming.update');
    Route::delete('/incoming/{id}', [IncomingDocumentController::class, 'destroy'])->name('incoming.destroy');

    // Outgoing documents
    Route::get('/outgoing', [OutgoingDocumentController::class, 'index'])->name('outgoing.index');
    Route::post('/outgoing/store', [OutgoingDocumentController::class, 'store'])->name('outgoing.store');
    Route::get('/outgoing/{id}/edit', [OutgoingDocumentController::class, 'edit'])->name('outgoing.edit');
    Route::put('/outgoing/{id}', [OutgoingDocumentController::class, 'update'])->name('outgoing.update');
    Route::delete('/outgoing/{id}', [OutgoingDocumentController::class, 'destroy'])->name('outgoing.destroy');

    // Common (Incoming ↔ Outgoing)
    Route::get('/documents/common', [CommonDocumentController::class, 'index'])
        ->name('documents.common');

    Route::get('/incoming/{id}/attachment', [IncomingDocumentController::class, 'downloadAttachment'])
        ->name('incoming.attachment');

    Route::get('/incoming/{id}/attachments', [IncomingDocumentController::class, 'attachmentsIndex'])
        ->name('incoming.attachments.index');

    Route::get('/incoming/{id}/attachments/{attachmentId}', [IncomingDocumentController::class, 'attachmentsView'])
        ->name('incoming.attachments.view');

    Route::get('/incoming/{id}/attachments/{attachmentId}/viewer', [IncomingDocumentController::class, 'attachmentsViewer'])
        ->name('incoming.attachments.viewer');

    Route::get('/outgoing/{id}/attachment', [OutgoingDocumentController::class, 'viewAttachment'])
        ->name('outgoing.attachment');

    Route::get('/outgoing/{id}/attachments', [OutgoingDocumentController::class, 'attachmentsIndex'])
        ->name('outgoing.attachments.index');

    Route::get('/outgoing/{id}/attachments/{attachmentId}', [OutgoingDocumentController::class, 'attachmentsView'])
        ->name('outgoing.attachments.view');

    Route::get('/outgoing/{id}/attachments/{attachmentId}/viewer', [OutgoingDocumentController::class, 'attachmentsViewer'])
        ->name('outgoing.attachments.viewer');

    Route::get('/attachments/tree', [AttachmentTreeController::class, 'index'])
        ->name('attachments.tree');
});

/*
|--------------------------------------------------------------------------
| Admin area (SUPERUSER ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'admin', 'protocolYear'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::prefix('incoming')->name('incoming.')->group(function () {
            Route::get('/', [IncomingDocumentController::class, 'index'])->name('index');

            Route::get('/{id}/edit', [IncomingDocumentController::class, 'edit'])->name('edit');
            Route::put('/{id}', [IncomingDocumentController::class, 'update'])->name('update');
        });

        Route::prefix('outgoing')->name('outgoing.')->group(function () {
            Route::get('/', [OutgoingDocumentController::class, 'index'])->name('index');
            Route::get('/outgoing/{id}/edit', [OutgoingDocumentController::class, 'edit'])->name('outgoing.edit');
            Route::put('/{id}', [OutgoingDocumentController::class, 'update'])->name('update');
        });

        // Users management
        Route::get('/users', [UserManagementController::class, 'index'])
            ->name('users.index');

        Route::get('/users/create', [UserManagementController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [UserManagementController::class, 'store'])
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [UserManagementController::class, 'update'])
            ->name('users.update');

        Route::put('/users/{user}/password', [UserManagementController::class, 'updatePassword'])
            ->name('users.password');

        // ✅ ΤΕΛΙΚΟ: Διαγραφή χρήστη (όχι απενεργοποίηση)
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
            ->name('users.destroy');

        // Audit log
        Route::get('/audit', [AuditLogController::class, 'index'])
            ->name('audit.index');
    });

require __DIR__.'/auth.php';

Route::get('/documents/all', [DocumentController::class, 'all'])
    ->middleware(['auth', 'active', 'protocolYear'])
    ->name('documents.all');

Route::get('incoming/attachment/{id}', [IncomingDocumentController::class, 'downloadAttachment'])->name('incoming.attachment');
Route::get('outgoing/attachment/{id}', [OutgoingDocumentController::class, 'viewAttachment'])->name('outgoing.attachment');

Route::get('/documents/print', [DocumentController::class, 'print'])
    ->middleware(['auth', 'active', 'protocolYear'])
    ->name('documents.print');
